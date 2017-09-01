#!/usr/bin/env python2
# spectrum module
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
import socket
import threading
import time
from collections import OrderedDict
from hashlib import md5
from subprocess import Popen, PIPE, STDOUT
from sys import builtin_module_names

from gammarf_base import GrfModuleBase

DEFAULT_SAMPLES = 100
ERROR_SLEEP = 3
HACKRF_LNA_GAIN = 32
HACKRF_MIN_SCAN_MHZ = 40
HACKRF_MAX_SCAN_MHZ = 1500
MODULE_DESCRIPTION = "spectrum module"
RTLSDR_CROP = 20  # %
RTLSDR_INTEGRATION_INTERVAL = 3
RTLSDR_WINDOW = "hamming"
THREAD_TIMEOUT = 3

device_list = ["rtlsdr", "hackrf"]


def start(config):
    return GrfModuleSpectrum(config)


class Spectrum(threading.Thread):
    def __init__(self, server_opts, spectrum_opts, gpsp, devmod, isref):
        threading.Thread.__init__(self)

        self.station_id = server_opts['station_id']
        self.station_pass = server_opts['station_pass']
        self.server_host = server_opts['server_host']
        self.server_port = server_opts['server_port']

        self.gpsp = gpsp
        self.devmod = devmod
        self.isref = isref

        self.sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        self.freqmap = dict()
        self.sent_first = False

        self.devnum = spectrum_opts['devnum']
        self.samples = spectrum_opts['samples']
        self.devtype = spectrum_opts['devtype']
        cmd = spectrum_opts['cmd']
        fstr = spectrum_opts['fstr']
        width = spectrum_opts['width']

        ON_POSIX = 'posix' in builtin_module_names 
        DEVNULL = open(os.devnull, 'w')

        if self.devtype == 'hackrf':
            if self.isref:
                lna_gain = 0
                vga_gain = 0
            else:
		lna_gain = spectrum_opts['lna_gain']
		vga_gain = spectrum_opts['vga_gain']

            self.cmdpipe = Popen([cmd, "-f{}".format(fstr), "-w {}".format(width),
                "-l {}".format(lna_gain), "-g {}".format(vga_gain)],
                stdout=PIPE, stderr=DEVNULL, close_fds=ON_POSIX)

        elif self.devtype == 'rtlsdr':
            integration = spectrum_opts['integration']
            gain = spectrum_opts['gain']
            ppm = spectrum_opts['ppm']

            ON_POSIX = 'posix' in builtin_module_names 
            self.cmdpipe = Popen([cmd, "-d {}".format(self.devnum), "-f {}".format(fstr),
                    "-i {}".format(integration), "-p {}".format(ppm), "-g {}".format(gain),
                    "-c {}%".format(RTLSDR_CROP), "-w {}".format(RTLSDR_WINDOW)],
                    stdout=PIPE, stderr=STDOUT, close_fds=ON_POSIX)

        self.stoprequest = threading.Event()

    def run(self):
        input_received = False
        numfreqs = 0
        numsent = 0

        if self.isref:
            jobid = 'reference'
        else:
            jobid = 'spectrum'

        while not self.stoprequest.isSet():
            data = self.cmdpipe.stdout.readline()

            if len(data) == 0:
                continue

            # look for gps here to avoid flooding the reporter in the case of no lock
            loc = self.gpsp.get_current()
            if (loc == None) or (loc['lat'] == "0.0" and loc['lng'] == "0.0") or (loc['lat'] == "NaN"):
                print("[spectrum] No GPS loc, waiting...")
                time.sleep(ERROR_SLEEP)
                continue

            for raw in data.split('\n'):
                if self.stoprequest.isSet():
                    break

                if len(raw) == 0:
                    continue

                if self.devtype == 'rtlsdr':
                    if len(raw.split(' ')[0].split('-')) != 3:  # line irrelevant, or from stderr
                        if raw == "Error: dropped samples.":
                            print("[spectrum] Error with device {}, exiting task".format(self.devnum))
                            self.devmod.removedev(self.devnum)
                            return
                        continue

                try:
                    _date, _time, freq_low, freq_high, step, _samples, raw_readings = raw.split(', ', 6)
                    freq_low = float(freq_low)
                    step = float(step)
                    readings = [x.strip() for x in raw_readings.split(',')]

                except Exception:
                    print("[spectrum] Thread exiting on exception")
                    return

                if not input_received:
                    print("[spectrum] Receiving input")
                    input_received = True

                for i in range(len(readings)):
                    freq = int(round(freq_low + (step * i)))
                    try:
                        pwr = float(readings[i])
                    except Exception as e:
                        print 'a: {}'.format(e)
                        continue

                    try:
                        fent = self.freqmap[freq]
                        if not fent:  # already sent stats
                            continue
                    except KeyError:
                        # mean, stdev, n, S, min, max, 
                        fent = [0, 0, 0, 0.0, 0, 0]
                        numfreqs += 1

                    # http://dsp.stackexchange.com/questions/811/determining-the-mean-and-standard-deviation-in-real-time
                    mean, _, n, S, pwrmax, pwrmin = fent
                    n += 1
                    prev_mean = mean
                    mean = mean + (pwr - mean) / n
                    S = S + (pwr - mean) * (pwr - prev_mean)
                    stdev = math.sqrt(S / n)

                    if pwr < pwrmin:
                        pwrmin = pwr

                    if pwr > pwrmax:
                        pwrmax = pwr

                    if n >= self.samples:
                        self.send_stats(freq, mean, stdev, pwrmax - pwrmin, jobid)
                        self.freqmap[freq] = None

                        numsent += 1
                        if numsent == numfreqs:
                            print '[spectrum] Finished.'
                            self.stoprequest.set()
                            break
                    else:
                        self.freqmap[freq] = [mean, stdev, n, S, pwrmin, pwrmax]

        self.cmdpipe.stdout.close()
        self.cmdpipe.kill()

        os.kill(self.cmdpipe.pid, 2)
        time.sleep(5)
        os.kill(self.cmdpipe.pid, 9)
        os.wait()

        # todo: only do ths if it's not a remotemod
        self.devmod.freedev(self.devnum)

        return

    def send_stats(self, freq, mean, stdev, rng, jobid):
        if not self.sent_first:
            print("[spectrum] Beginning to send data")
            self.sent_first = True

        data = OrderedDict()
        data['freq'] = freq
        data['mean'] = '%.3f'%(mean)  # str
        data['stdev'] = '%.3f'%(stdev)
        data['range'] = '%.3f'%(rng)
        data['ct'] = int(time.time())
        data['stationid'] = self.station_id
        data['module'] = 'sp'
        data['jobid'] = jobid

        # just basic sanity
        m = md5()
        m.update(self.station_pass + str(data['mean']) + str(data['ct']))
        data['sign'] = m.hexdigest()[:12]

        try:
            self.sock.sendto(json.dumps(data), (self.server_host, self.server_port))
        except Exception as e:
            print("[spectrum] Error sending to server: {}".format(e))

    def join(self, timeout=None):
        self.stoprequest.set()
        super(Spectrum, self).join(timeout)


class GrfModuleSpectrum(GrfModuleBase):
    def __init__(self, config):
        rtl_path = config.rtldevs.rtl_path
        if not isinstance(rtl_path, str) or not rtl_path:
            raise Exception("param 'rtl_path' not appropriately defined in config")

        rtlcmd = rtl_path + '/' + 'rtl_power'
        if not os.path.isfile(rtlcmd) or not os.access(rtlcmd, os.X_OK):
            raise Exception("executable rtl_power not found in specified path")

        hackrf_path = config.hackrfdevs.hackrf_path
        if not isinstance(hackrf_path, str) or not hackrf_path:
            raise Exception("param 'hackrf_path' not appropriately defined in config")

        hackrfcmd = hackrf_path + '/' + 'hackrf_sweep'
        if not os.path.isfile(hackrfcmd) or not os.access(hackrfcmd, os.X_OK):
            raise Exception("executable hackrf_sweep not found in specified path")

        self.rtlcmd = rtlcmd
        self.hackrfcmd = hackrfcmd

        samples = config.spectrum.samples
        if not isinstance(samples, str) or not samples:
            self.samples = DEFAULT_SAMPLES
        else:
            self.samples = int(samples)

        self.settings = {}

        self.spectrum = None

        print("Loading {}".format(MODULE_DESCRIPTION))

    def help(self):
        print("Spectrum: Report statistics about large swaths of bandwidth")
        print("")
        print("Usage: spectrum devnum freqs isref")
        print("\tWhere freqs is a frequency range in rtl_power format.")
        print("\tand isref is an optional param that means this is a reference (input terminated; no antenna) scan.")
        print("\tExample: > run spectrum 0 200M:300M:15k 1")
        print("")
        print("\tSettings:")
        return True

    def run(self, devnum, args, system_params, loadedmods, remotetask=False):
        self.remotetask = remotetask
        devmod = loadedmods['devices']

        if self.spectrum and not self.spectrum.isAlive():
            print("Spectrum already running (one allowed per node)")
            return

        server_opts = {'station_id': system_params['station_id'],
                'station_pass': system_params['station_pass'],
                'server_host': system_params['server_host'],
                'server_port': system_params['server_port']}

        self.gpsworker = loadedmods['location'].gps_worker

        if not args:
            print("Must include a frequency specification")
            return

        try:
            freqs, isref = args.split(' ')
            isref = True
        except:
            freqs = args
            isref = False
            
        freqs = freqs.strip()
        if len(freqs.split(':')) != 3:
            print("Bad frequency specification")
            return

        devtype = devmod.get_devtype(devnum)
        if devtype == "rtlsdr":
            try:
                outfreqs = []
                lowfreq, highfreq, width = freqs.split(':')
                for f in [lowfreq, highfreq]:
                    if f[len(f)-1] == 'M':
                        f = int(float(f[:len(f)-1])*1e6)
                    elif f[len(f)-1] == 'k':
                        f = int(float(f[:len(f)-1])*1e3)
                    else:
                        f = int(f)
                    outfreqs.append(f)
            except Exception:
                print("Error parsing frequency string")
                return

            if outfreqs[1] < outfreqs[0]:
                print("Second scan frequency must be greater than the first scan frequency")
                return

            fstr = "{}:{}:{}".format(str(outfreqs[0]), str(outfreqs[1]), width)

            spectrum_opts = {'cmd': self.rtlcmd,
                    'devnum': devnum,
                    'fstr': fstr,
                    'integration': RTLSDR_INTEGRATION_INTERVAL,
                    'ppm': devmod.get_ppm(devnum),
                    'gain': devmod.get_gain(devnum),
                    'settings': self.settings}

        elif devtype == "hackrf":
            try:
                outfreqs = []
                lowfreq, highfreq, width = freqs.split(':')
                for f in [lowfreq, highfreq]:
                    if f[len(f)-1] == 'M':
                        f = int(float(f[:len(f)-1]))
                    elif f[len(f)-1] == 'k':
                        print("Use frequencies with an 'M' (MHz) suffix")
                        return
                    else:
                        print("Use frequencies with an 'M' (MHz) suffix")
                        return
                    outfreqs.append(f)

                if outfreqs[1] < outfreqs[0]:
                    print("Second scan frequency must be greater than the first scan frequency")
                    return

                if outfreqs[1] - outfreqs[0] < HACKRF_MIN_SCAN_MHZ:
                    print("You must scan at least {} MHz when using HackRF.".format(HACKRF_MIN_SCAN_MHZ))
                    return

                if outfreqs[1] - outfreqs[0] > HACKRF_MAX_SCAN_MHZ:
                    print("You may scan at most {} MHz when using HackRF.".format(HACKRF_MAX_SCAN_MHZ))
                    return

                if width[len(width)-1] == 'M':
                    width = int(float(width[:len(width)-1]) * 1e6)
                elif width[len(width)-1] == 'k':
                    width = int(float(width[:len(width)-1]) * 1e3)
                else:
                    width = int(width)

            except Exception:
                print("Error parsing frequency string")
                return

            fstr = "{}:{}".format(str(outfreqs[0]), str(outfreqs[1]))

            spectrum_opts = {'cmd': self.hackrfcmd,
                    'devnum': devnum,
                    'fstr': fstr,
                    'width': width,
                    'lna_gain': devmod.get_lna_gain(devnum),
                    'vga_gain': devmod.get_vga_gain(devnum)}

        spectrum_opts['samples'] = self.samples
        spectrum_opts['devtype'] = devtype

        spectrum = Spectrum(server_opts, spectrum_opts, self.gpsworker, devmod, isref)
        #spectrum.daemon = True
        self.spectrum = spectrum
        print("Spectrum added on device {}".format(devnum))
        self.spectrum.start()

        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        print("Shutting down spectrum module")

        if self.spectrum:
		self.spectrum.join(THREAD_TIMEOUT)
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
        if self.spectrum.isAlive():
            self.spectrum.join(THREAD_TIMEOUT)

            if not self.remotetask:
                devmod.freedev(devnum)

            return True

        return False

    def ispseudo(self):
        return False

    def devices(self):
        return device_list
