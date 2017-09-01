#!/usr/bin/env python2
# remotetask module
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
import threading
from json import dumps, loads
from socket import socket, timeout, AF_INET, SOCK_STREAM
from time import sleep, time

from gammarf_base import GrfModuleBase

BUFSZ = 1024
CONNECT_SLEEP = 60  # s
ERROR_SLEEP = 3
JOBSTOP_SLEEP = 3
LOOP_SLEEP = 5
MODULE_DESCRIPTION = "remotetask module"
THREAD_TIMEOUT = 4
SOCKET_TIMEOUT = 8


class RemoteTaskDispatcher(threading.Thread):
    def __init__(self, system_params, devnum, module, loadedmods,  gpsworker, settings):
        self.system_params = system_params
        self.station_id = system_params['station_id']
        self.devnum = devnum
        self.module = module.split(' ', 1)[0].strip()
        self.jobmodule = loadedmods[module]
        self.loadedmods = loadedmods
        self.devmod = loadedmods['devices']
        self.gpsworker = gpsworker
	self.settings = settings

        self.stoprequest = threading.Event()
        threading.Thread.__init__(self)

    def run(self):
        request = dict()
        request['request'] = 'getdtask'
        request['stationid'] = self.station_id
        request['accept'] = self.module

        while not self.stoprequest.isSet():
            loc = self.gpsworker.get_current()
            lat = loc['lat']
            lng = loc['lng']

            if (loc == None) or (lat == "0.0" and lng == "0.0") or (lat == "NaN"):
                sleep(ERROR_SLEEP)
                continue

            request['lat'] = float(lat)
            request['lng'] = float(lng)

            sock = socket(AF_INET, SOCK_STREAM)
            sock.settimeout(SOCKET_TIMEOUT)
            try:
                sock.connect( (self.system_params['server_host'], self.system_params['server_port']) )
            except Exception as e:
                print("[remotetask] Could not connect to server: {} -- sleeping".format(e))
                sleep(CONNECT_SLEEP)
                continue

            try:
                sock.send(dumps(request))
                response = sock.recv(BUFSZ)
            except timeout:
                print("[remotetask] Socket timeout.  Is the command line correct?")
                break

            sock.close()

            try:
                task = loads(response)
            except:
                print("[remotetask] Error communicating with the backend, stopping module")
                break

            if task['reply'] == 'none':
                sleep(LOOP_SLEEP)
                continue

            if task['reply'] == 'ok':
                mod = task['mod']
                params = task['params']
                duration = int(task['duration'])

            if self.jobmodule.run(self.devnum, params, self.system_params, self.loadedmods, remotetask=True):
                started = time()
                if self.settings['print_tasks']:
                    print("Starting remote task: {} {}".format(mod, params))

                while True:
                    if time() - started >= duration or self.stoprequest.isSet():
                        self.jobmodule.stop(self.devnum, self.devmod)
                        sleep(JOBSTOP_SLEEP)
                        break

                    sleep(LOOP_SLEEP)
                    continue

            else:
                sleep(LOOP_SLEEP)
                continue

        self.jobmodule.stop(self.devnum, self.devmod)
        self.devmod.freedev(self.devnum)
        return

    def join(self, timeout=None):
        self.stoprequest.set()
        super(RemoteTaskDispatcher, self).join(timeout)


def start(config):
    return GrfModuleRemotetask(config)


class GrfModuleRemotetask(GrfModuleBase):
    def __init__(self, config):
        self.workers = list()

        self.settings = {'print_tasks': False}

        print("Loading {}".format(MODULE_DESCRIPTION))
        return

    def request(self, system_params, reqline, stationid, gpsworker):
        tmp = reqline.split(' ')

        if len(tmp) < 7:
            print("A request must be made in this format: > run remotetask devnum request requested_module module_params lat lng range duration (NOTE: devnum doesn't matter)")
            return

        request = dict()
        request['request'] = 'putdtask'
        request['stationid'] = stationid
        request['module'] = tmp[1]
        request['params'] = tmp[2]
        request['lat'] = float(tmp[3])
        request['lng'] = float(tmp[4])
        request['range'] = float(tmp[5])
        request['duration'] = tmp[6]

        sock = socket(AF_INET, SOCK_STREAM)
        sock.settimeout(SOCKET_TIMEOUT)
        try:
            sock.connect( (system_params['server_host'], system_params['server_port']) )
        except Exception as e:
            print("[remotetask] Could not connect to server: {}".format(e))
            return

        try:
            sock.send(dumps(request))
            response = sock.recv(BUFSZ)
        except timeout:
            print("[remotetask] Socket timeout.  Is the command line correct?")
            return
        print(response)
        sock.close()

        return

    def help(self):
        print("Remotetask: Accept tasks on  behalf of the community")
        print("")
        print("Usage: remotetask rtl_devnum module")
        print("\tWhere 'module' is the class of module you want to accept (eg. 'scanner')")
        print("")
        print("\tIf module is 'request', send a task to the queue.")
        print("\t> gammarf run remotetask 0 request module moduleparams lat lng gps_range job_duration")
        print("\t Coordinates are where you'd like the job to run.  If there are no stations in the area")
        print("\t you defined with lat, lng, and range, then the job cannot run (until a station is set up there)")
        print("")
        print("\tSettings:")
        print("\t\tprint_tasks: List remote tasks your node grabs from the queue")
        return True

    def run(self, devnum, cmdline, system_params, loadedmods):
        if cmdline == None:
            print("Must specify a valid module.  Type 'mods' for command usage")
            return

        tmp = cmdline.split(' ')
        if len(tmp) > 1:
            module, args = cmdline.split(' ', 1)
        else:
            module = cmdline

        if not module in loadedmods.keys() and module != 'request':
            print("{} not a valid module".format(module))
            return

        gpsworker = loadedmods['location'].gps_worker
        stationid = system_params['station_id']
        
        if module == 'request':
            self.request(system_params, cmdline, stationid, gpsworker)
            return

        rtdispatcher = RemoteTaskDispatcher(system_params, devnum, module, loadedmods, gpsworker, self.settings)
        rtdispatcher.daemon = True
        rtdispatcher.start()
        self.workers.append( (devnum, rtdispatcher) )

        print("Remotetask module added for device {}".format(devnum))
        return True

    def report(self):
        return

    def info(self):
        return

    def shutdown(self):
        print("Shutting down remotetask module")

        for worker in self.workers:
            devnum, thread = worker
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
        for worker in self.workers:
            worker_devnum, thread = worker

            if worker_devnum == devnum:
                thread.join(THREAD_TIMEOUT)
                devmod.freedev(devnum)
                self.workers.remove( (worker_devnum, thread) )
                return True

        return False

    def ispseudo(self):
        return False

    def devices(self):
        return None
