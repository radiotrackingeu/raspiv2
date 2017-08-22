#!/usr/bin/env python2
# snapshot module
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
import os
import socket
import threading
import time
from collections import OrderedDict
from hashlib import md5
from subprocess import Popen, PIPE, STDOUT
from sys import builtin_module_names
from uuid import uuid4

from gammarf_base import GrfModuleBase

CROP = 20  # %
ERROR_SLEEP = 3  # s

MAXBW = 200e6  # keep e notation
MINFREQ = 30e6
MAXFREQ = 1600e6
MINSTEP = 5e3
MAXSTEP = 1e6
MIN_EXPOSURE = 15
MAX_EXPOSURE = 120
WINDOW = "hamming"

MODULE_DESCRIPTION = "snapshot module"
REPORTER_SLEEP = 5/100  # limit activity reporting so as not to saturate the server
THREAD_TIMEOUT = 7

procs = list()
device_list = ["rtlsdr"]


def start(config):
    return GrfModuleSnapshot(config)

 
class Snapshot(threading.Thread):
    def __init__(self, snapshot_opts, gpsp, devmod):
        global procs

        self.server_host = snapshot_opts['server_host']
        self.server_port = snapshot_opts['server_port']
        self.station_id = snapshot_opts['station_id']
        self.station_pass = snapshot_opts['station_pass']
        self.devnum = snapshot_opts['devnum']
        self.hzhigh = snapshot_opts['hzhigh']
        self.gpsp = gpsp
        self.devmod = devmod
        self.socket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

        cmd = snapshot_opts['cmd']
        hzlow = snapshot_opts['hzlow']
        hzstep = snapshot_opts['hzstep']
        exposure_time = snapshot_opts['exposure_time']
        ppm = snapshot_opts['ppm']
        gain = snapshot_opts['gain']

        print("[snapshot]: scanning from {} to {}".format(hzlow, self.hzhigh))
        fstr = "{}:{}:{}".format(hzlow, self.hzhigh, hzstep)
        ON_POSIX = 'posix' in builtin_module_names 
        self.cmdpipe = Popen([cmd, "-d {}".format(self.devnum), "-f {}".format(fstr),
                "-i {}".format(exposure_time), "-p {}".format(ppm), "-g {}".format(gain),
                "-c {}%".format(CROP), "-w {}".format(WINDOW), "-1"],
                stdout=PIPE, stderr=STDOUT, close_fds=ON_POSIX)

        procs.append(self.cmdpipe)
        threading.Thread.__init__(self)

    def run(self):
        snapshotid = str(uuid4())
        final = False
        running = True
        while running:
            data = self.cmdpipe.stdout.readline()

            if len(data) == 0:
                try:
                    continue
                except Exception:
                    return

            loc = self.gpsp.get_current()
            if (loc == None) or (loc['lat'] == "0.0" and loc['lng'] == "0.0") or (loc['lat'] == "NaN"):
                print("[snapshot] No GPS loc, waiting...")
                time.sleep(ERROR_SLEEP)
                continue

            for line in data.split('\n'):
                if len(line.split(' ')[0].split('-')) != 3:  # line irrelevant, or from stderr
                    if line == "Error: dropped samples.":
                        print("[snapshot] Error with device {}, exiting task".format(self.devnum))
                        self.devmod.removedev(self.devnum)
                        return
                    continue

                if not line.startswith(str(datetime.date.today().year)):
                    continue

                #ct = int(round(time.time() * 1000))

                tmp = line.split(',')
                if (float(tmp[3]) + float(tmp[4])) > float(self.hzhigh):
                    final = True

                self.send(line, loc, snapshotid, final) #ct, final)

                if final:
                    running = False

        print("[snapshot]: sent snapshot (id: {}) at {}".format(snapshotid, datetime.datetime.now()))

        for proc in procs:
            proc.wait()

        self.devmod.freedev(self.devnum)
        return

    def send(self, line, loc, snapshotid, final): #, ct, final):
        data = OrderedDict()
        data['stationid'] = self.station_id
        data['lat'] = loc['lat']
        data['lng'] = loc['lng']
        data['module'] = 'ss'
        data['snapshotid'] = snapshotid
        data['final'] = str(final)
        #data['ct'] = ct
        data['rawdata'] = line

        # just basic sanity
        data['time'] = str(int(time.time()))
        m = md5()
        m.update(self.station_pass + data['time'])
        data['sign'] = m.hexdigest()[:12]

        self.socket.sendto(json.dumps(data), (self.server_host, self.server_port))

        if not final:
            time.sleep(REPORTER_SLEEP)

        return


class GrfModuleSnapshot(GrfModuleBase):
    def __init__(self, config):
        rtl_path = config.rtldevs.rtl_path
        if not isinstance(rtl_path, str) or not rtl_path:
            raise Exception("param 'rtl_path' not appropriately defined in config")

        command = rtl_path + '/' + 'rtl_power'
        if not os.path.isfile(command) or not os.access(command, os.X_OK):
            raise Exception("executable rtl_power not found in specified path")

        self.config = config
        self.cmd = command

        self.snapshot_thread = None

        print("Loading {}".format(MODULE_DESCRIPTION))

    def help(self):
        print("Snapshot: Take a snapshot of the RF spectrum")
        print("")
        print("Usage: snapshot rtl_devnum hzlow hzhigh hzstep exposure_time")
        print("\tWhere hzlow is the start frequency,")
        print("\t\thzhigh is the end frequency,")
        print("\t\thzstep is the step (bin size),")
        print("\t\tand exposure_time is the time (in seconds) to integrate (watch the spectrum).")
        print("")
        print("\tFrequencies should be in valid rtl_power command line form.")
        print("")
        print("\tExample: > run snapshot 0 30e6 300e6 5e3 30")
        return True

    def run(self, devnum, cmdline, system_params, loadedmods, remotetask=False):
        self.remotetask = remotetask

        devmod = loadedmods['devices']

        try:
            hzlow, hzhigh, hzstep, exposure_time = cmdline.split()
            hzlow = float(hzlow)
            hzhigh = float(hzhigh)
            hzstep = float(hzstep)
            exposure_time = int(exposure_time)
        except:
            print("Must specify a valid low frequency, high frequency, frequency step, and exposure time.  Type 'mods' for command usage.")
            return

        if not (MINFREQ <= hzlow <= MAXFREQ) or not (hzhigh <= MAXFREQ) or (hzhigh < hzlow):
            print("The minimum frequency is {}, the maximum is {}.  Hzlow must be lower than hzhigh.".format(MINFREQ, MAXFREQ))
            return

        if hzhigh - hzlow > MAXBW:
            print("Snapshot bandwidth cannot exceed {}".format(MAXBW))
            return

        if not MINSTEP <= hzstep <= MAXSTEP:
            print("The step must be between {} and {}.".format(MINSTEP, MAXSTEP))
            return

        if exposure_time < MIN_EXPOSURE or exposure_time > MAX_EXPOSURE:
            print("Exposure time must be between {} and {}".format(MIN_EXPOSURE, MAX_EXPOSURE))
            return

        snapshot_opts = {
                'server_host': system_params['server_host'],
                'server_port': system_params['server_port'],
                'station_id': system_params['station_id'],
                'station_pass': system_params['station_pass'],
                'cmd': self.cmd,
                'devnum': devnum,
                'hzlow': hzlow,
                'hzhigh': hzhigh,
                'hzstep': hzstep,
                'exposure_time': exposure_time,
                'ppm': devmod.get_ppm(devnum),
                'gain': devmod.get_gain(devnum)
                }

        snapshot = Snapshot(snapshot_opts, loadedmods['location'].gps_worker, devmod)
        snapshot.daemon = True
        snapshot.start()
        self.snapshot_thread = snapshot

        print("Snapshot running on device {}".format(devnum))

        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        global procs

        if self.snapshot_thread:
            print("Waiting for the snapshot process to finish")
            self.snapshot_thread.join()

        for proc in procs:
            proc.wait()

        return

    def showconfig(self):
        return

    def setting(self, setting, arg=None):
        print("There are no settings for the snapshot module.")
        return True

    def stop(self, devnum, devmod):
        if self.snapshot_thread:
            print("Waiting for the snapshot process to finish")
            self.snapshot_thread.join()

        if not self.remotetask:
            devmod.freedev(devnum)

        return True

    def ispseudo(self):
        return False

    def devices(self):
        return device_list
