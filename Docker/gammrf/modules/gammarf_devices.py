#!/usr/bin/env python
# devices module
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
import rtlsdr
import subprocess
from ctypes import c_ubyte, string_at
from time import sleep, strftime

from gammarf_base import GrfModuleBase

DEFAULT_GAIN = 16.6
MODULE_DESCRIPTION = "devices module"


def start(config):
    return GrfModuleDevices(config)


class SDRDev(object):
    def __init__(self):
        self.devtype = None
        self.devnum = 0
        self.name = None
        self.job = None
        self.usable = True
        self.serial = None
        self.reserved = False

        # rtlsdr
        self.gain = 0
        self.ppm = 0

        # hackrf
        self.lna_gain = 0
        self.vga_gain = 0


class GrfModuleDevices(GrfModuleBase):
    def __init__(self, config):
        print("Loading {}".format(MODULE_DESCRIPTION))

        devs = dict()
        devnums = dict()

        # do it this way because there's no mature python hackrf lib out yet
        # assume one device
        hrfproc = subprocess.Popen("hackrf_info", shell=True)
        hrfproc.wait()
        hackrf = False
        if hrfproc.returncode == 0:
            hackrf = True
            devnum = 0

            hrfdev = SDRDev()
            hrfdev.devnum = devnum
            hrfdev.serial = "HackRF{}".format(devnum)
            hrfdev.name = "{} {}".format(devnum, hrfdev.serial)
            hrfdev.devtype = "hackrf"

            if isinstance(config.hackrfdevs.lna_gain, str):
                hrfdev.lna_gain = int(config.hackrfdevs.lna_gain)

            if isinstance(config.hackrfdevs.vga_gain, str):
                hrfdev.vga_gain = int(config.hackrfdevs.vga_gain)

            devs[0] = hrfdev

        self.rtlsdr_devcount = rtlsdr.librtlsdr.rtlsdr_get_device_count()
        if not self.rtlsdr_devcount and not hackrf:
            print("-- Found no usable devices")
            exit()

        for devnum in range(len(devs), self.rtlsdr_devcount):
            rtldev = SDRDev()
            rtldev.devtype = "rtlsdr"
            rtldev.devnum = devnum

            buffer1 = (c_ubyte * 256)()
            buffer2 = (c_ubyte * 256)()
            serial = (c_ubyte * 256)()
            rtlsdr.librtlsdr.rtlsdr_get_device_usb_strings(devnum, buffer1, buffer2, serial)
            serial = string_at(serial)
            devname = "{} {} {}".format(devnum,
                    rtlsdr.librtlsdr.rtlsdr_get_device_name(devnum),
                    serial)
           
            rtldev.name = devname
            rtldev.serial = serial
            print devname

            stickppm = eval("config.rtldevs.ppm_{}".format(serial))
            if isinstance(stickppm, str):
                rtldev.ppm = int(stickppm)

            stickgain = eval("config.rtldevs.gain_{}".format(serial))
            if isinstance(stickgain, str):
                rtldev.gain = float(stickgain)
            else:
                rtldev.gain = float(DEFAULT_GAIN)

            devs[devnum] = rtldev

        self.devs = devs

    def get_devtype(self, devnum):
        dev = self.devs[devnum]
        return dev.devtype

    def get_ppm(self, devnum):
        dev = self.devs[devnum]
        return dev.ppm

    def get_gain(self, devnum):
        dev = self.devs[devnum]
        return dev.gain

    def get_lna_gain(self, devnum):
        dev = self.devs[devnum]
        return dev.lna_gain

    def get_vga_gain(self, devnum):
        dev = self.devs[devnum]
        return dev.vga_gain

    def isdev(self, devnum):
        if not self.devs.has_key(devnum):
            return False
        return True
        
    def get_devs(self):
        return [dtup[1].name for dtup in self.devs.items()]

    def occupied(self, devnum):
        if not self.devs.has_key(devnum):
            return False

        dev = self.devs[devnum]
        if dev.job:
            return True
        return False

    def occupy(self, devnum, module, cmdline=None, pseudo=False):
        if pseudo:
            if not self.devs.has_key(devnum):
                rtldev = SDRDev()
                rtldev.devnum = devnum
                rtldev.name = "{} Pseudo device".format(devnum)
                self.devs[devnum] = rtldev

        dev = self.devs[devnum]
        if dev.job or not dev.usable:
            return False
        dev.job = (module, cmdline, strftime("%c"))
        return True

    def devnum_to_module(self, devnum):
        if not self.occupied(devnum):
            return

        dev = self.devs[devnum]
        
        if not dev.usable:
            return

        module, _, _ = dev.job
        return module

    def freedev(self, devnum):
        dev = self.devs[devnum]
        dev.job = None
        return

    def removedev(self, devnum):
        dev = self.devs[devnum]
        dev.job = "*** Out of commission"
        dev.usable = False
        return

    def reserve(self, devnum):
        if devnum in self.devs:
            dev = self.devs[devnum]
            dev.reserved = True
            dev.job = "*** Reserved"
        return

    def unreserve(self, devnum):
        dev = self.devs[devnum]
        dev.reserved = False
        dev.job = None
        return

    def reserved(self, devnum):
        if not self.isdev(devnum):
            return False

        dev = self.devs[devnum]
        return dev.reserved

    # ABC functions
    def help(self):
        return

    def run(self, cmdline, system_params):
        return

    def report(self):
        return

    def info(self):
        for devtuple in self.devs.items():
            dev = devtuple[1]
            print("{} - {}".format(dev.name, dev.job if dev.job else "Unoccupied"))

    def shutdown(self):
        return

    def showconfig(self):
        return

    def setting(self, setting):
        return

    def stop(self, devnum, devmod):
        return

    def ispseudo(self):
        return False

    def devices(self):
        return None
