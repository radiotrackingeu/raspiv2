#!/bin/bash

#stop expand systems on reboot

OLDSTRING='@reboot root \/usr\/bin\/raspi-config --expand-rootfs'
NEWSTRING='#@reboot root \/usr\/bin\/raspi-config --expand-rootfs'
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE

OLDSTRING='@reboot root sh \/var\/www\/html\/git\/stop_expand.sh'
NEWSTRING='#@reboot root sh \/var\/www\/html\/git\/stop_expand.sh'
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE