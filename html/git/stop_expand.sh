#!/bin/bash

#/usr/bin/raspi-config --expand-rootfs

OLDSTRING='@reboot root /usr/bin/raspi-config --expand-rootfs'
NEWSTRING='#@reboot root /usr/bin/raspi-config --expand-rootfs'
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE

OLDSTRING='@reboot root sh /var/www/html/git/stop_expand.sh'
NEWSTRING='#@reboot root sh /var/www/html/git/stop_expand.sh'
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE