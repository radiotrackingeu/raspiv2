#!/usr/bin/env python2
# ads-b module
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
import json
import os
import socket
import threading
import time
import pyModeS as pms
from collections import OrderedDict
from hashlib import md5
from subprocess import Popen, PIPE
from sys import builtin_module_names

from gammarf_base import GrfModuleBase

ERROR_SLEEP = 3
MODULE_DESCRIPTION = "adsb module"
THREAD_TIMEOUT = 3

device_list = ["rtlsdr"]


def start(config):
    return GrfModuleAdsb(config)


class Adsb(threading.Thread):
    def __init__(self, adsb_opts, gpsp, devmod, settings):
        self.devnum = adsb_opts['devnum']
        self.gpsp = gpsp
        self.devmod = devmod
        cmd = adsb_opts['cmd']
        ppm = adsb_opts['ppm']
        self.station_id = adsb_opts['station_id']
        self.station_pass = adsb_opts['station_pass']
        self.server_host = adsb_opts['server_host']
        self.server_port = adsb_opts['server_port']
        self.settings = settings

        ON_POSIX = 'posix' in builtin_module_names 
        self.cmdpipe = Popen([cmd, "-d {}".format(self.devnum), "-p {}".format(ppm)],
                stdout=PIPE, close_fds=ON_POSIX)

        self.stoprequest = threading.Event()
        threading.Thread.__init__(self)

    def run(self):
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

        outmsg = OrderedDict()
        outmsg['stationid'] = self.station_id

        poscache = dict()

        while not self.stoprequest.isSet():
            # look for gps here to avoid flooding the reporter in the case of no lock
            loc = self.gpsp.get_current()
            if (loc == None) or (loc['lat'] == "0.0" and loc['lng'] == "0.0") or (loc['lat'] == "NaN"):
                print("[adsb] No GPS loc, waiting...")
                time.sleep(ERROR_SLEEP)
                continue

            msg = self.cmdpipe.stdout.readline().strip()

            if len(msg) == 0:
                continue

            if not msg.startswith('*') or not msg.endswith(';'):
                continue

            msg = msg[1:-1]
            if len(msg) != 28:
                continue

            crc = pms.util.hex2bin(msg[-6:])
            p = pms.util.crc(msg, encode=True)
            if p != crc:
                continue

            df = pms.df(msg)  # downlink format
            if df == 17:  # ads-b
                tc = pms.adsb.typecode(msg)  # type code
                if 1 <= tc <= 4:  # identification message
                    icao = pms.adsb.icao(msg)
                    callsign = pms.adsb.callsign(msg)

                    if self.settings['print_all']:
                        print("[adsb] (ID) ICAO: {}, Callsign: {}]".format(icao, callsign))

                    outmsg['icao'] = icao
                    outmsg['callsign'] = callsign.strip('_')
                    outmsg['aircraft_lat'] = None
                    outmsg['aircraft_lng'] = None
                    outmsg['altitude'] = None
                    outmsg['heading'] = None
                    outmsg['updownrate'] = None
                    outmsg['speedtype'] = None
                    outmsg['speed'] = None

                elif 9 <= tc <= 18:  # airborne position
                    icao = pms.adsb.icao(msg)
                    altitude = pms.adsb.altitude(msg)

                    if icao in poscache.keys() and poscache[icao]:
                        (recent_tc, recent_msg, recent_time) = poscache[icao]
                        if recent_tc != tc:
                            poscache[icao] = None
                            continue

                        recent_odd = (pms.util.hex2bin(recent_msg)[53] == '1')
                        msg_odd = (pms.util.hex2bin(msg)[53] == '1')

                        if recent_odd != msg_odd:
                            if recent_odd:
                                oddmsg = recent_msg
                                evenmsg = msg
                                t_odd = recent_time
                                t_even = time.time()
                            else:
                                oddmsg = msg
                                evenmsg = recent_msg
                                t_odd = time.time()
                                t_even = recent_time

                            pos = pms.adsb.position(oddmsg, evenmsg, t_odd, t_even)
                            if not pos:
                                continue
                            lat, lng = pos

                        else:
                            poscache[icao] = (tc, msg, time.time())
                            continue

                    else:
                        poscache[icao] = (tc, msg, time.time())
                        continue
                    
                    if self.settings['print_all']:
                        print("[adsb] (POS) ICAO: {}, Lat: {}, Lng: {}, Alt: {}".format(icao, lat, lng, altitude))

                    outmsg['icao'] = icao
                    outmsg['callsign'] = None
                    outmsg['aircraft_lat'] = lat
                    outmsg['aircraft_lng'] = lng
                    outmsg['altitude'] = altitude
                    outmsg['heading'] = None
                    outmsg['updownrate'] = None
                    outmsg['speedtype'] = None
                    outmsg['speed'] = None

                elif tc == 19:  # airborne velocities
                    icao = pms.adsb.icao(msg)
		    velocity = pms.adsb.velocity(msg)
                    speed, heading, updownrate, speedtype = velocity

                    if self.settings['print_all']:
                        print("[adsb] (VEL) ICAO: {}, Heading: {}, ClimbRate: {}, Speedtype: {}, Speed: {}".format(icao, heading, updownrate, speedtype, speed))

                    outmsg['icao'] = icao
                    outmsg['callsign'] = None
                    outmsg['aircraft_lat'] = None
                    outmsg['aircraft_lng'] = None
                    outmsg['altitude'] = None
                    outmsg['heading'] = heading
                    outmsg['updownrate'] = updownrate
                    outmsg['speedtype'] = speedtype
                    outmsg['speed'] = speed

                else:
                    continue

                outmsg['lat'] = float(loc['lat'])
                outmsg['lng'] = float(loc['lng'])
                outmsg['module'] = 'ad'

                outmsg['time'] = str(int(time.time()))
                m = md5()
                m.update(self.station_pass + outmsg['icao'] + outmsg['time'])
                outmsg['sign'] = m.hexdigest()[:12]

                try:
                    sock.sendto(json.dumps(outmsg), (self.server_host, self.server_port))
                except Exception:
                    print("[adsb] Could not send to server, waiting...")
                    time.sleep(ERROR_SLEEP)
                    continue

        sock.close()
        self.cmdpipe.stdout.close()
        self.cmdpipe.kill()
        os.kill(self.cmdpipe.pid, 9)
        os.wait()

        return

    def join(self, timeout=None):
        self.stoprequest.set()
        super(Adsb, self).join(timeout)


class GrfModuleAdsb(GrfModuleBase):
    def __init__(self, config):
        rtl_path = config.rtldevs.rtl_path
        if not isinstance(rtl_path, str) or not rtl_path:
            raise Exception("param 'rtl_path' not appropriately defined in config")

        command = rtl_path + '/' + 'rtl_adsb'
        if not os.path.isfile(command) or not os.access(command, os.X_OK):
            raise Exception("executable rtl_adsb not found in specified path")

        self.cmd = command
        self.settings = {'print_all': False}
        self.adsbthread = None

        print("Loading {}".format(MODULE_DESCRIPTION))

    def help(self):
        print("ADS-B: Report received flight information")
        print("")
        print("Usage: adsb rtl_devnum")
        print("")
        print("\tSettings:")
        print("\t\tprint_all: Print flight messages as they're intercepted")
        return True

    def run(self, devnum, _freqs, system_params, loadedmods, remotetask=False):
        self.remotetask = remotetask

        if self.adsbthread:
            print("ADS-B already running (one allowed per node)")
            return

        devmod = loadedmods['devices']
        self.gpsworker = loadedmods['location'].gps_worker
        adsb_opts = {'cmd': self.cmd,
                'ppm': devmod.get_ppm(devnum),
                'station_id': system_params['station_id'],
                'station_pass': system_params['station_pass'],
                'server_host': system_params['server_host'],
                'server_port': system_params['server_port'],
                'devnum': devnum}


        self.adsbthread = Adsb(adsb_opts, self.gpsworker, devmod, self.settings)
        self.adsbthread.daemon = True
        self.adsbthread.start()

        print("ADS-B listener added on device {}".format(devnum))
        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        print("Shutting down adsb module(s)")
        if self.adsbthread:
            self.adsbthread.join(THREAD_TIMEOUT)
            self.adsbthread = None

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
        if self.adsbthread:
            self.adsbthread.join(THREAD_TIMEOUT)

        if not self.remotetask:
            devmod.freedev(devnum)

        self.adsbthread = None
        return True

    def ispseudo(self):
        return False

    def devices(self):
        return device_list
