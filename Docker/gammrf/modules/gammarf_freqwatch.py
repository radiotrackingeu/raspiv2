#!/usr/bin/env python2
# freqwatch module
#
# Joshua Davis (gammarf -*- covert.codes)
# http://gammarf.io
# Copyright(C) 2016
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
import json
import math
import rtlsdr
import socket
import threading
import time
import matplotlib.mlab as mlab
from collections import OrderedDict
from numpy import hamming
from hashlib import md5
from uuid import uuid4

from gammarf_base import GrfModuleBase

DEFAULT_GAIN = 36.4
DWELL = 0.1
ERROR_SLEEP = 3  # s
LOOP_DELAY = 0.1
MODULE_DESCRIPTION = "freqwatch module"
OFFSET = 200e3
NFFT = 1024
SAMPLE_RATE = 2.4e6
THREAD_TIMEOUT = 5

device_list = ["rtlsdr"]


def start(config):
    return GrfModuleFreqwatch(config)


class Monitor(threading.Thread):
    def __init__(self, opts, gpsp, devmod, settings):
        self.devnum = opts['devnum']
        self.station_id = opts['station_id']
        self.station_pass = opts['station_pass']
        self.server_host = opts['server_host']
        self.server_port = opts['server_port']
        self.freqlist = opts['freqlist']
        self.jobid = opts['uuid']

        self.sdr = rtlsdr.RtlSdr(self.devnum)
        self.sdr.set_sample_rate(SAMPLE_RATE)
        self.sdr.set_manual_gain_enabled(1)

        ppm = opts['ppm']
        if ppm != 0:
            self.sdr.freq_correction = ppm

        self.gpsp = gpsp
        self.devmod = devmod
        self.settings = settings
        self.gain = self.settings['gain']
        self.sdr.set_gain(self.gain)
        self.numsamps = self.next_2_to_pow(int(DWELL * SAMPLE_RATE))

        self.stoprequest = threading.Event()
        threading.Thread.__init__(self)

    def run(self):
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        outmsg = OrderedDict()
        outmsg['stationid'] = self.station_id

        while not self.stoprequest.isSet():
            loc = self.gpsp.get_current()
            if (loc == None) or (loc['lat'] == "0.0" and loc['lng'] == "0.0") or (loc['lat'] == "NaN"):
                print("[freqwatch] No GPS loc, waiting...")
                time.sleep(ERROR_SLEEP)
                continue

            if self.gain != self.settings['gain']:
                self.gain = self.settings['gain']
                self.sdr.set_gain(self.gain)

            for freq in self.freqlist:
                freq = freq - OFFSET  # avoid dc spike
                try:
                    self.sdr.set_center_freq(freq)
                    samples = self.sdr.read_samples(self.numsamps)
                except IOError:
                    continue
                except Exception as e:
                    print 'Exception in freqwatch module: {}'.format(e)

                if not len(samples):
                    continue

                powers, freqs = mlab.psd(samples, NFFT=NFFT, Fs=SAMPLE_RATE/1e6, window=hamming(NFFT))
                bin_offset = int(OFFSET/(SAMPLE_RATE / NFFT))
                freq = int(freq + OFFSET)
                pwr = "{:.2f}".format(10 * math.log10( powers[ int(len(powers)/2) + bin_offset ] ))

                if self.settings['print_all']:
                    print("[freqwatch] Freq: {}, Pwr: {}, Lat: {}, Lng: {}".format(freq, pwr, loc['lat'], loc['lng']))

                outmsg['freq'] = freq
                outmsg['pwr'] = float(pwr)
                outmsg['lat'] = float(loc['lat'])
                outmsg['lng'] = float(loc['lng'])
                outmsg['module'] = 'fw'
                #outmsg['jobid'] = self.jobid
                outmsg['time'] = str(int(time.time()))
                m = md5()
                m.update(self.station_pass + str(outmsg['pwr']) + outmsg['time'])
                outmsg['sign'] = m.hexdigest()[:12]

                try:
                    sock.sendto(json.dumps(outmsg), (self.server_host, self.server_port))
                except Exception:
                    print("[freqwatch] Could not send to server, waiting...")
                    time.sleep(ERROR_SLEEP)
                    continue

            time.sleep(LOOP_DELAY)

        self.sdr.close()
        sock.close()
        return

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
        super(Monitor, self).join(timeout)


class GrfModuleFreqwatch(GrfModuleBase):
    def __init__(self, config):
        self.monitors = list()
        self.config = config

        self.gpsworker = None
        self.settings = {'print_all': False,
                'gain': DEFAULT_GAIN}

        print("Loading {}".format(MODULE_DESCRIPTION))

    def help(self):
        print("Freqwatch: Periodically report power on a specified frequency")
        print("")
        print("Usage: freqwatch rtl_devnum freq1 ... freqn")
        print("\tExample: > run freqwatch 0 200M 210M")
        print("")
        print("\tYou can use sets in the configuration file (gammarf.conf)")
        print("\tExample: set0 = 123.4, 456.7")
        print("\t> run freqwatch 0 set0")
        print("\tSettings:")
        print("\t\tprint_all: Print power readings to console")
        print("\t\tgain: RTL-SDR gain")
        return True

    def run(self, devnum, cmdline, system_params, loadedmods, remotetask=False):
        self.remotetask = remotetask
        devmod = loadedmods['devices']

        if not self.gpsworker:
            self.gpsworker = loadedmods['location'].gps_worker

        if not cmdline:
            self.usage()
            return

        tmpfreqs = None
        first = cmdline.split()[0].strip()
        if first[:3] == 'set':
            setnum = first[3:]

            try:
                fstr = eval("self.config.freqwatch.set{}".format(setnum))
                if isinstance(fstr, str):
                    tmpfreqs = [f.strip() for f in fstr.split(',')]
            except:
                print("Frequency set in configuration seems malformed")
                return

        if not tmpfreqs:
            tmpfreqs = cmdline.split()

        freqlist = list()
        for freq in tmpfreqs:
            try:
                if freq[len(freq)-1] == 'M':
                    freq = int(float(freq[:len(freq)-1])*1e6)
                elif freq[len(freq)-1] == 'k':
                    freq = int(float(freq[:len(freq)-1])*1e3)
                else:
                    freq = int(freq)

            except Exception:
                print("Frequencies should be numeric, and may include the suffixes 'M' and 'k'")
                return

            freqlist.append(freq)

        opts = {'station_id': system_params['station_id'],
                'station_pass': system_params['station_pass'],
                'server_host': system_params['server_host'],
                'server_port': system_params['server_port'],
                'devnum': devnum,
                'ppm': devmod.get_ppm(devnum),
                'freqlist': freqlist,
                'uuid': str(uuid4())}

        monitor = Monitor(opts, self.gpsworker, devmod, self.settings)
        monitor.daemon = True
        monitor.start()
        self.monitors.append( (devnum, monitor) )

        print("Monitor added on device {}".format(devnum))
        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        print("Shutting down freqwatch module(s)")
        for monitor in self.monitors:
            devnum, thread = monitor
            thread.join(THREAD_TIMEOUT)

        return

    def showconfig(self):
        return

    def setting(self, setting, arg=None):
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
        for monitor in self.monitors:
            monitor_devnum, thread = monitor
            if monitor_devnum == devnum:
                thread.join(THREAD_TIMEOUT)

                if not self.remotetask:
                    devmod.freedev(devnum)

                self.monitors.remove( (monitor_devnum, thread) )
                return True

        return False

    def usage(self):
        print("Must include a frequency list on the command line (eg. > freqwatch 0 200M 210M)")
        return

    def ispseudo(self):
        return False

    def devices(self):
        return device_list
