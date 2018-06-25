#!/bin/bash

#stop expand systems on reboot
sed -i '\|^@reboot.*raspi-config --expand-rootfs.*$|d' /etc/crontab