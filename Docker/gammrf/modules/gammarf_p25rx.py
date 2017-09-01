#!/usr/bin/env python2
# p25 receiver module
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
import errno
import json
import socket
import threading
import time
from collections import OrderedDict
from hashlib import md5
from uuid import uuid4

from gammarf_base import GrfModuleBase

CALL_REGEX = r'[\S\s]+Call created for: ([0-9]+) [\S\s]+'
ERROR_SLEEP = 3  # s
LST_ADDR = "127.0.0.1"
MODULE_DESCRIPTION = "p25 receiver module"
SOCK_BUFSZ  = 1024
THREAD_TIMEOUT  = 1


def start(config):
    return GrfModuleP25Receiver(config)


class P25Rx(threading.Thread):
    def __init__(self, opts, gpsp, devmod, settings):
        self.station_id = opts['station_id']
        self.station_pass = opts['station_pass']
        self.server_host = opts['server_host']
        self.server_port = opts['server_port']
        self.port = opts['port']
        self.jobid = opts['uuid']
        self.gpsp = gpsp
        self.devmod = devmod
        self.settings = settings


        threading.Thread.__init__(self)

    def run(self):
        srvsock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        self.lstsock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

        try:
            self.lstsock.bind( ('', self.port) )
        except Exception as e:
            print("[p25rx] Could not listen on port {}: {}".format(self.port, e))
            return

        outmsg = OrderedDict()
        outmsg['stationid'] = self.station_id

        while True:
            loc = self.gpsp.get_current()
            if (loc == None) or (loc['lat'] == "0.0" and loc['lng'] == "0.0") or (loc['lat'] == "NaN"):
                print("[p25rx] No GPS loc, waiting...")
                time.sleep(ERROR_SLEEP)
                continue

            for line in self.lstsock.makefile():
                if '\t' in line:
                    _, msg = line.split('\t', 1)
                else:
                    continue

                if msg.startswith('Recording'):
                    talkgroup = msg.split(' ')[-1].strip()
                else:
                    continue

                if self.settings['print_all']:
                    print("[p25rx] Talkgroup: {}, Lat: {}, Lng: {}".format(talkgroup, loc['lat'], loc['lng']))

                outmsg['talkgroup'] = talkgroup
                outmsg['lat'] = float(loc['lat'])
                outmsg['lng'] = float(loc['lng'])
                outmsg['module'] = 'p25'
                outmsg['jobid'] = self.jobid
                outmsg['time'] = str(int(time.time()))
                m = md5()
                m.update(self.station_pass + outmsg['talkgroup'] + outmsg['time'])
                outmsg['sign'] = m.hexdigest()[:12]

                try:
                    srvsock.sendto(json.dumps(outmsg), (self.server_host, self.server_port))
                except Exception:
                    print("[p25rx] Could not send to server, waiting...")
                    time.sleep(ERROR_SLEEP)
                    continue

        return

    def join(self, timeout=None):
        self.lstsock.close()
        super(P25Rx, self).join(timeout)


class GrfModuleP25Receiver(GrfModuleBase):
    def __init__(self, config):
        self.config = config
        self.threads = list()

        self.settings = {'print_all': False}

        print("Loading {}".format(MODULE_DESCRIPTION))

    def help(self):
        print("P25Receiver: Parse trunk-recorder lines received on a UDP port")
        print("")
        print("Usage: p25rx rtl_devnum port")
        print("\trtl_devnum doesn't matter (this is a pseudo-module)")
        print("\tExample: > run p25rx 0 51000")
        print("")
        print("\tSettings:")
        print("\t\tprint_all: Print each talkgroup as its identified")
        return True

    def run(self, devnum, cmdline, system_params, loadedmods, remotetask=False):
        self.remotetask = remotetask
        devmod = loadedmods['devices']
        self.devnum = devnum
        self.gpsworker = loadedmods['location'].gps_worker

        if not cmdline:
            self.usage()
            return

        try:
            port = int(cmdline.strip())
        except Exception:
            print("Bad port number given")
            return

        opts = {'station_id': system_params['station_id'],
                'station_pass': system_params['station_pass'],
                'server_host': system_params['server_host'],
                'server_port': system_params['server_port'],
                'devnum': devnum,
                'port': port,
                'uuid': str(uuid4())}

        self.p25rx = P25Rx(opts, self.gpsworker, devmod, self.settings)
        self.p25rx.daemon = True
        self.p25rx.start()
        self.threads.append(self.p25rx)

        print("P25Rx added on device {}".format(devnum))
        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        print("Shutting down p25rx module")
        for thread in self.threads:
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
        for thread in self.threads:
            thread.join(THREAD_TIMEOUT)
            devmod.freedev(self.devnum)
        return True

    def usage(self):
        print("Must include a port on the command line (eg. > p25rx 9000 51000)")
        return

    def ispseudo(self):
        return True

    def devices(self):
        return None
