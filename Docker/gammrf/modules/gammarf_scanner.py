#!/usr/bin/env python2
# scanner module
#
# Joshua Davis (gammarf -*- covert.codes)
# http://gammarf.io
# Copyright(C) 2017
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.

from __future__ import division

import abc
import datetime
import json
import math
import os
import rtlsdr
import socket
import threading
import time
import matplotlib.mlab as mlab
from collections import OrderedDict
from hashlib import md5
from numpy import hamming

from gammarf_base import GrfModuleBase

AVG_SAMPLES = 75  # how many samples to avg before registering hits ###############################
CMD_BUFSZ = 32768
CMDSOCK_TIMEOUT = 10
DEFAULT_GAIN = 36.4
DEFAULT_HIT_DB = 6.0
DWELL = 0.008  # s
ERROR_SLEEP = 3
INTERESTING_QUERY_INT = 60
MODULE_DESCRIPTION = "scanner module"
OFFSET = 200e3
NFFT = 1024
SAMPLE_RATE = 2.4e6
THREAD_TIMEOUT = 3

device_list = ["rtlsdr"]


def start(config):
    return GrfModuleScanner(config)


class Scanner(threading.Thread):
    def __init__(self, scanner_opts, gpsp, devmod, settings):
        self.station_id = scanner_opts['station_id']
        self.station_pass = scanner_opts['station_pass']
        self.server_host = scanner_opts['server_host']
        self.server_port = scanner_opts['server_port']
        self.devnum = scanner_opts['devnum']

        self.gpsp = gpsp
        self.devmod = devmod
        self.settings = settings

        self.freqmap = dict()
        self.socket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        self.interesting = []
        self.since_interesting = None

        self.sdr = rtlsdr.RtlSdr(self.devnum)
        self.sdr.set_sample_rate(SAMPLE_RATE)
        self.sdr.set_manual_gain_enabled(1)

        ppm = scanner_opts['ppm']
        if ppm != 0:
            self.sdr.freq_correction = ppm

        self.gain = self.settings['gain']
        self.sdr.set_gain(self.gain)
        self.numsamps = self.next_2_to_pow(int(DWELL * SAMPLE_RATE))

        self.stoprequest = threading.Event()
        threading.Thread.__init__(self)

    def run(self):
        ready = False

        while not self.stoprequest.isSet():
            if (not self.since_interesting) or ( (datetime.datetime.utcnow() - self.since_interesting).total_seconds() >= INTERESTING_QUERY_INT ):
                # get interesting freqs from the server (they may have updated)
                request = dict()
                request['request'] = 'getinteresting'
                request['stationid'] = self.station_id

                try:
                    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                    sock.settimeout(CMDSOCK_TIMEOUT)
                    sock.connect( (self.server_host, self.server_port) )
                    sock.send(json.dumps(request))
                    response = sock.recv(CMD_BUFSZ)
                    sock.close()
                except Exception as e:
                    print("[scanner] Could not get interesting freqs from the server: {}".format(e))
                    time.sleep(ERROR_SLEEP)
                else:
                    try:
                        tmp = json.loads(response)
                    except ValueError:
                        continue

                    if tmp['reply'] == 'ok':
                        tmpfreqs = [ int(f) for f in tmp['freqs'] ]

                        if tmpfreqs != self.interesting:
                            self.interesting = tmpfreqs
                            print("[scanner] Interesting freqs changed")
                            print self.interesting

                    self.since_interesting = datetime.datetime.utcnow()

            loc = self.gpsp.get_current()
            if (loc == None) or (loc['lat'] == "0.0" and loc['lng'] == "0.0") or (loc['lat'] == "NaN"):
                print("[scanner] No GPS loc, waiting...")
                time.sleep(ERROR_SLEEP)
                continue

            if self.gain != self.settings['gain']:
                self.gain = self.settings['gain']
                self.sdr.set_gain(self.gain)

            ct = int(time.time())
            for freq in self.interesting:
                # TODO: check for accuracy, make more efficient

                freq = freq - OFFSET
                try:
                    self.sdr.set_center_freq(freq)
                    samples = self.sdr.read_samples(self.numsamps)
                except IOError:
                    continue
                except Exception as e:
                    print 'Exception in scanner module: {}'.format(e)

                if not len(samples):
                    continue

                powers, freqs = mlab.psd(samples, NFFT=NFFT, Fs=SAMPLE_RATE/1e6, window=hamming(NFFT))
                bin_offset = int(OFFSET/(SAMPLE_RATE / NFFT))
                freq = int(freq + OFFSET)
                pwr = float("{:.2f}".format(10 * math.log10( powers[ int(len(powers)/2) + bin_offset ] )))

                try:
                    fent = self.freqmap[freq]
                except KeyError:
                    fent = {'mean': 0, 'stdev': 0, 'n': 0, 'S': 0}

                # http://dsp.stackexchange.com/questions/811/determining-the-mean-and-standard-deviation-in-real-time
                prev_mean = fent['mean']
                fent['n'] = fent['n'] + 1
                fent['mean'] = fent['mean'] + (pwr - fent['mean']) / fent['n']
                fent['S'] = fent['S'] + (pwr - fent['mean']) * (pwr - prev_mean)
                fent['stdev'] = math.sqrt(fent['S']/fent['n'])

                if fent['n'] > AVG_SAMPLES:
                    if not ready and freq == self.interesting[-1]:
                        print("Initial scanner means formulated.")
                        ready = True

                    squelch = fent['mean'] + self.settings['hit_db']
                    if pwr > squelch:
                        self.send_hit(freq, pwr, loc, ct)
                        if self.settings['print_hits']:
                            print("[scanner] Hit on {} ({} > {}), stdev: {}".format(freq, pwr, squelch, fent['stdev']))

                self.freqmap[freq] = fent

        self.sdr.close()
        sock.close()

        return

    def send_hit(self, freq, pwr, loc, ct):
        data = OrderedDict()
        data['lat'] = loc['lat']
        data['lng'] = loc['lng']
        data['freq'] = freq
        data['pwr'] = str(pwr)
        data['ct'] = ct

        data['stationid'] = self.station_id
        data['module'] = 'sc'

        # just basic sanity
        m = md5()
        m.update(self.station_pass + str(data['pwr']) + str(data['ct']))
        data['sign'] = m.hexdigest()[:12]

        self.socket.sendto(json.dumps(data), (self.server_host, self.server_port))

    def next_2_to_pow(self, val):
        val -= 1
        val |= val >> 1
        val |= val >> 2
        val |= val >> 4
        val |= val >> 8
        val |= val >> 16
        return val + 1

    def join(self, timeout=None):
        self.stoprequest.set()
        super(Scanner, self).join(timeout)


class GrfModuleScanner(GrfModuleBase):
    def __init__(self, config):
        hit_db = config.scanner.hit_db
        if not isinstance(hit_db, str) or not hit_db:
            hit_db = DEFAULT_HIT_DB
        else:
            hit_db = float(hit_db)

        self.scannerthread = None

        self.settings = {'print_hits': False,
                'hit_db': hit_db,
                'gain': DEFAULT_GAIN}

        print("Loading {}".format(MODULE_DESCRIPTION))

    def help(self):
        print("Scanner: Report deviations in average power on interesting freqs to the backend")
        print("")
        print("Usage: scanner devnum")
        print("\tThe list of interesting freqs is retrieved from the server.")
        print("\tExample: > run scanner 0")
        print("")
        print("\tSettings:")
        print("\t\tprint_hits: Print hits (activity on an 'interesting' freq)")
        print("\t\thit_db: Power is required to be this high above the average (dB) to be considered a hit")
        print("\t\tgain: RTL-SDR gain")
        return True

    def run(self, devnum, freqs, system_params, loadedmods, remotetask=False):
        self.remotetask = remotetask

        if self.scannerthread:
            print("Scanner already running (one allowed per node)")
            return

        devmod = loadedmods['devices']
        self.gpsworker = loadedmods['location'].gps_worker

        scanner_opts = {'station_id': system_params['station_id'],
                'station_pass': system_params['station_pass'],
                'server_host': system_params['server_host'],
                'server_port': system_params['server_port'],
                'ppm': devmod.get_ppm(devnum),
                'devnum': devnum}

        self.scannerthread = Scanner(scanner_opts, self.gpsworker, devmod, self.settings)
        self.scannerthread.daemon = True
        self.scannerthread.start()

        print("Scanner added on device {}".format(devnum))
        print("[scanner] NOTE: It takes awhile to gather samples to form an average, for new frequency ranges")

        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        print("Shutting down scanner module")
        if self.scannerthread:
            self.scannerthread.join(THREAD_TIMEOUT)
            self.scannerthread = None

        return

    def showconfig(self):
        return

    def setting(self, setting, arg=None):
        if not self.scannerthread:
            print("Module not ready")
            return True

        if setting == None:
            for setting, state in self.settings.items():
                print("{}: {} ({})".format(setting, state, type(state)))
            return True

        if setting == 0:
            return self.settings.keys()

        if setting not in self.settings.keys():
            return False

        if isinstance(self.settings[setting], bool):
            new = not self.settings[setting]
        elif not arg:
            print("Non-boolean setting requires an argument")
            return True
        else:
            if isinstance(self.settings[setting], int):
                new = int(arg)
            elif isinstance(self.settings[setting], float):
                new = float(arg)
            else:
                new = arg

        self.settings[setting] = new

        return True

    def stop(self, devnum, devmod):
        if self.scannerthread:
            self.scannerthread.join(THREAD_TIMEOUT)

        if not self.remotetask:
            devmod.freedev(devnum)

        self.scannerthread = None
        return True

    def ispseudo(self):
        return False

    def devices(self):
        return device_list
