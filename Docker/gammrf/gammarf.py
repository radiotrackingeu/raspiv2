#!/usr/bin/env python2
# -*- coding: iso-8859-15 -*-
# ΓRF client
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
import cmd
import sys
from importlib import import_module
from iniparse import INIConfig
from time import sleep

from modules import gammarf_base

CONF_FILE      = 'gammarf.conf'
ERR            = 1
PSEUDO_DEVNUM_BASE = 9000
REQD_MODULES   = ['devices', 'location']
VERSION_STRING = "ΓRF v1.1.0, Copyright 2017 (gammarf |at| covert.codes)"

MODPATH        = 'modules'
MOD_PREFIX     = 'gammarf_'
MOD_BASE       = 'base'


def main():
    try:
        config = INIConfig(open(CONF_FILE))
    except Exception:
        print("Error: Could not open configuration file '{}', exiting".format(CONF_FILE))
        exit(ERR)

    # Get list of modules to load, verify the required ones exist
    if not isinstance(config.modules.modules, str) or not config.modules.modules:
        print("Error: No modules listed in configuration file, exiting")
        exit(ERR)

    modules = [m.strip() for m in config.modules.modules.split(',')]

    for reqd in REQD_MODULES:
        if not reqd in modules:
            print("Error: required module '{}' not set to be loaded, exiting".format(reqd))
            exit(ERR)

    # Get system params, verify their sanity
    system_params = dict()
    system_params['station_id']   = config.station.stationid
    system_params['station_pass'] = config.station.stationpass
    system_params['server_host'] = config.server.host
    system_params['server_port'] = config.server.port

    for key, value in system_params.items():
        if not isinstance(value, str):
            print("Error: param '{}' not appropriately defined in the configuration file, exiting.".format(key))
            exit(ERR)

    system_params['server_port'] = int(system_params['server_port'])

    # Load modules
    sys.path.append(MODPATH)

    loadedmods = dict()
    for module in modules:
        modsource = MOD_PREFIX + module

        try:
            Mod = import_module(modsource)
            ModObj = Mod.start(config, )
            loadedmods[module] = ModObj

        except Exception as e:
            if module in REQD_MODULES:
                print("Could not load required module '{}': {}, exiting.".format(module, e))
                exit(ERR)
            else:
                print("Warning: could not load module '{}': {}.".format(module, e))

    print("Loaded modules: {}".format(loadedmods.keys()))
    print("")

    for devnum, dev in loadedmods['devices'].devs.iteritems():
        serial = dev.serial
        cmdline = eval("config.startup.startup_{}".format(serial))
        if isinstance(cmdline, str):
            try:
                module, args = cmdline.split(' ', 1)
            except Exception:
                module = cmdline.strip()
                args = None

            if module in REQD_MODULES or module not in loadedmods.keys():
                continue

            if args == '':
                args = None
                continue

            if loadedmods[module].run(devnum, args, system_params, loadedmods):
                loadedmods['devices'].occupy(devnum, module, args)

    # pseudo-devices
    devnum = PSEUDO_DEVNUM_BASE
    while True:
        cmdline = eval("config.startup.startup_{}".format(devnum))
        if isinstance(cmdline, str):
            try:
                module, args = cmdline.split(' ', 1)
            except Exception:
                module = cmdline.strip()
                args = None

            if module in REQD_MODULES or module not in loadedmods.keys():
                continue

            if args == '':
                args = None
                continue

            if loadedmods[module].run(devnum, args, system_params, loadedmods):
                loadedmods['devices'].occupy(devnum, module, args, pseudo=True)

            devnum += 1
        else:
            break

    cmd = Interpreter()
    cmd.system_params = system_params
    cmd.loadedmods = loadedmods

    cmd.cmdloop()

class Interpreter(cmd.Cmd):
    prompt = 'ΓRF> '

    logo = """
   _______________
   \   __________/
    |  |_____________________
    |  |\______   \_   _____/
    |  | |       _/|    __)
    |  | |    |   \|     \\
    |  | |____|_  /\___  /
    |  |        \/     \/
    \__/

    """
    intro = "{}\n\n {}\n (^D to quit)\n".format(logo, VERSION_STRING)

    doc_header = "Commands (type 'help command' for more information about a specific command)"
    ruler = "="

    def do_config(self, line):
        """Print general and module configure information"""
        print(VERSION_STRING)

        stationid = self.system_params['station_id']
        print("Station {}".format(stationid))

        host = self.system_params['server_host']
        port = self.system_params['server_port']
        print("Sending to {} on port {}".format(host, port))

        for module in self.loadedmods:
            self.loadedmods[module].showconfig()

    def do_devs(self, line):
        """Show RTL-SDR devices and their current occupation"""
        self.loadedmods['devices'].info()
        return

    def do_location(self, line):
        """Show current node's location information"""
        self.loadedmods['location'].info()
        return

    def do_mods(self, line):
        """List modules and their usage descriptions"""
        for module in self.loadedmods:
            print(module)
            print('='*len(module))
            if not self.loadedmods[module].help():
                print("No description available for module")
            print("")

    def do_nodes(self, line):
        """Show currently active ΓRF client nodes (locations only)"""
        print("Not yet implemented")
        return

    def do_run(self, line):
        """Run a module; include parameters as shown from the 'mods' command"""
        devmod = self.loadedmods['devices']

        parsed = line.split(" ", 2)
        if parsed[0] == '' or parsed[0] == '':
            print("Type 'help run' and 'mods' for command usage")
            return

        module = parsed[0]
        if module not in self.loadedmods.keys():
            print("Invalid module: {}".format(module))
            return

        if module in REQD_MODULES:
            return

        try:
            devnum = int(parsed[1])
        except:
            print("Must include a device number.  Type 'help run' for command usage")
            return

        if devmod.reserved(devnum):
            print("Device reserved: {}".format(devnum))
            return

        if len(parsed) > 2 and parsed[2] != '':
            cmdline = parsed[2]
        else:
            cmdline = None

        pseudo = False
        if self.loadedmods[module].ispseudo():
            if devnum < PSEUDO_DEVNUM_BASE:
                print("Pseudo modules must use devnum >= {}".format(PSEUDO_DEVNUM_BASE))
                return
            pseudo = True
        else:
            if not devmod.isdev(devnum):
                print("Not a device: {}".format(devnum))
                return

        devtype = devmod.get_devtype(devnum)
        if not devtype in self.loadedmods[module].devices():
            print("Device type {} not supported by module".format(devtype))
            return

        if not devmod.occupied(devnum) or (module == 'remotetask' and (cmdline and cmdline.split(' ', 1)[0] == 'request') ):

            if self.loadedmods[module].run(devnum, cmdline, self.system_params, self.loadedmods):
                devmod.occupy(devnum, module, cmdline, pseudo)
        else:
            print("Cannot run module: device {} occupied".format(devnum))
            return

    def complete_run(self, text, line, begidx, endidx):
        if line.count(' ') > 2:
            return
        elif line.count(' ') > 1:
            return [str(m) for m in self.loadedmods['devices'].get_devs()]
        elif not text:
            return self.loadedmods.keys()[:]
        else:
            return [m for m in self.loadedmods if m.startswith(text)]

    def do_reserve(self, line):
        """Mark a device 'reserved' (useful if you're using it with another program)"""
        try:
            devnum = int(line)
        except Exception:
            print("Type 'help reserve' for command usage")
            return

        if self.loadedmods['devices'].occupied(devnum):
            print("Cannot reserve: device {} occupied".format(devnum))
            return

        self.loadedmods['devices'].reserve(devnum)
        return

    def complete_reserve(self, text, line, begidx, endidx):
        if line.count(' ') > 1:
            return

        return [str(m) for m in self.loadedmods['devices'].get_devs()]

    def do_unreserve(self, line):
        """Unreserve a device"""
        try:
            devnum = int(line)
        except Exception:
            print("Type 'help reserve' for command usage")
            return

        self.loadedmods['devices'].unreserve(devnum)
        return

    def complete_unreserve(self, text, line, begidx, endidx):
        if line.count(' ') > 1:
            return

        return [str(m) for m in self.loadedmods['devices'].get_devs()]

    def do_settings(self, line):
        """Show or toggle a setting for a module (> settings module_name [setting_name])"""
        parsed = line.split(" ")
        if parsed[0] == '' or len(parsed) > 3:
            print("Type 'help settings' for command usage")
            return

        module = parsed[0]
        if module not in self.loadedmods.keys():
            print("Invalid module: {}".format(module))
            return

        if len(parsed) == 1:  # show all
            self.loadedmods[module].setting(None)
        else: # toggle specified
            if len(parsed) == 2 and parsed[1] != '': # boolean arg
                result = self.loadedmods[module].setting(parsed[1])
            else:
                if len(parsed) == 3 and parsed[2] != '':
                    result = self.loadedmods[module].setting(parsed[1], parsed[2])

            if result == None:
                print("Module {} has no toggleable settings".format(module))
                return

    def complete_settings(self, text, line, begidx, endidx):
        if line.count(' ') > 2:
            return
        elif line.count(' ') > 1:
            module = line.split(' ')[1].strip()
            setting = line.split(' ')[2].strip()

            settings = self.loadedmods[module].setting(0)
            if setting:
                return [str(s) for s in settings if s.startswith(setting)]
            else:
                return settings[:]
        elif not text:
            return self.loadedmods.keys()[:]
        else:
            return [m for m in self.loadedmods if m.startswith(text)]

    def do_stop(self, line):
        """Stop a task occupying a device (> stop devnum)"""
        try:
            devnum = int(line)
        except Exception:
            print("Type 'help stop' for command usage")
            return

        module = self.loadedmods['devices'].devnum_to_module(devnum)
        if module:
            if self.loadedmods[module].stop(devnum, self.loadedmods['devices']):
                print("Done")
            else:
                print("Device not occupied by module")
        else:
            print("Device {} not occupied".format(devnum))

    def complete_stop(self, text, line, begidx, endidx):
        if line.count(' ') > 1:
            return

        return [str(m) for m in self.loadedmods['devices'].get_devs()]

    def do_EOF(self, line):
        """Exit ΓRF client"""
        i = raw_input("Exit? ").lower().strip()
        if i == 'y':
            # call stop one by one on each module, print the name each time so we know if lockup where, and exit
            for module in self.loadedmods:
                self.loadedmods[module].shutdown()

            return True
        else:
            return

    def default(self, line):
        line = line.strip()

        if line[0] == '#':  # comment
            return

        cmd = line.split(" ")[0]
        print("Bad command: {}".format(cmd))
        return

    def emptyline(self):
        return


if __name__ == '__main__':
    main()
