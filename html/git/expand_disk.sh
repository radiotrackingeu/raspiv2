#!/bin/bash

#expand systems on reboot

OLDSTRING='#@reboot root /usr/bin/raspi-config --expand-rootfs'
NEWSTRING='@reboot root /usr/bin/raspi-config --expand-rootfs'
FILE="/tmp/crontab"
grep -q "$NEWSTRING" $FILE && 
    sed -i "s|^$OLDSTRING|$NEWSTRING|g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE