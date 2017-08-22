#!/usr/bin/env python
# module base
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


class GrfModuleBase(object):
    __metaclass__ = abc.ABCMeta
    
    @abc.abstractmethod
    def help(self):
        """Print usage information"""
        return

    @abc.abstractmethod
    def run(self, cmdline, system_params, remotetask):
        """Module worker"""
        return

    @abc.abstractmethod
    def report(self):
        """Module communication to the grf backend"""
        return

    @abc.abstractmethod
    def info(self):
        """Return relevant information / output"""
        return

    @abc.abstractmethod
    def shutdown(self):
        """Cleanup code"""
        return

    @abc.abstractmethod
    def showconfig(self):
        """Show configuration params"""
        return

    @abc.abstractmethod
    def setting(self):
        """Toggle module setting"""
        return

    @abc.abstractmethod
    def stop(self, devnum, devmod):
        """Stop worker"""
        return

    @abc.abstractmethod
    def ispseudo(self):
        """Is pseudo module?"""
        return

    @abc.abstractmethod
    def devices(self):
        """Which devices do we support?"""
        return
