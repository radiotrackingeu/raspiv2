#!/bin/bash

#expand system partition on reboot

FILE="/tmp/crontab"
printf "@reboot root /usr/bin/raspi-config --expand-rootfs\n" >> $FILE
printf "@reboot root sed -i '\|^@reboot.*raspi-config --expand-rootfs.*$|d' /etc/crontab\n#" >> $FILE