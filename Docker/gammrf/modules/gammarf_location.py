#!/usr/bin/env python
# location module
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

import abc
import gps
import threading
from time import sleep

from gammarf_base import GrfModuleBase

ERROR_SLEEP = 5
MODULE_DESCRIPTION = "location module"


def start(config):
    return GrfModuleLocation(config)


class StaticGpsWorker():
    def __init__(self, gpslat, gpslng):
        self.lat = gpslat
        self.lng = gpslng

    def get_current(self):
        return {'lat': self.lat, 'lng': self.lng}


class GpsWorker(threading.Thread):
    def __init__(self):
        self.gpsd = gps.gps(mode=gps.WATCH_ENABLE)
        self.current = None

        self.running = True
        threading.Thread.__init__(self)

    def get_current(self):
        return {'lat': str(self.gpsd.fix.latitude),
                'lng': str(self.gpsd.fix.longitude)}

    def run(self):
        while self.running:
            try:
                self.gpsd.next()
            except StopIteration:
                print("GPS error, sleeping...")
                sleep(ERROR_SLEEP)
            except Exception:
                print("GPS error, sleeping...")
                sleep(ERROR_SLEEP)

    def stop(self):
        self.running = False


class GrfModuleLocation(GrfModuleBase):
    def __init__(self, config):
        print("Loading {}".format(MODULE_DESCRIPTION))

        usegps = config.location.usegps
        if not isinstance(usegps, str) or not usegps:
            raise Exception("param 'usegps' not appropriately defined in config")

        self.usegps = int(usegps)
        if self.usegps == 0:
            staticlat = config.location.lat
            staticlng = config.location.lng

            if not isinstance(staticlat, str) or not staticlat or\
                    not isinstance(staticlng, str) or not staticlng:
                        raise Exception("GPS off, but static location not defined in config")

            gps_worker = StaticGpsWorker(staticlat, staticlng)
            print("-- Using static location")

        else:
            gps_worker = GpsWorker()
            gps_worker.daemon = True
            gps_worker.start()
            print("-- Using GPS")

        self.gps_worker = gps_worker

    def help(self):
        return

    def run(self, cmdline, system_params, loadedmods):
        return

    def report(self):
        return self.gps_worker.get_current()

    def info(self):
        fix = self.gps_worker.get_current()
        lat = fix['lat']
        lon = fix['lng']
        print("Currently at Lat: {}, Lng: {}".format(lat, lon))

    def shutdown(self):
        if self.usegps:
            print("Shutting down GPS")
            self.gps_worker.stop()

    def showconfig(self):
        if self.usegps:
            print("-- Using static location")
        else:
            print("-- Using GPS")

        fix = self.gps_worker.get_current()
        print("Lat: {}, Lng: {}".format(fix['lat'], fix['lng']))

    def setting(self, setting):
        return

    def stop(self, devnum, devmod):
        return

    def ispseudo(self):
        return False

    def devices(self):
        return None
