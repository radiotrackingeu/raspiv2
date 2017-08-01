#!/bin/bash

# change /etc/crontab and comment docker wifi line
OLDSTRING='@reboot root docker run -d --rm --privileged --net=host wifi'
NEWSTRING='#@reboot root docker run -d --rm --privileged --net=host wifi'
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE
# change ssid and password

OLDSTRING=$(grep ssid /tmp/wpa_supplicant/wpa_supplicant.conf)
NEWSTRING="ssid=\"$1\""
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE
	
OLDSTRING=$(grep psk /tmp/wpa_supplicant/wpa_supplicant.conf)
NEWSTRING="psk=\"$2\""
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE