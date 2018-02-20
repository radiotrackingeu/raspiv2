#!/bin/bash

# change /etc/crontab and uncomment docker wifi line
# change /etc/crontab and comment docker wifi line
OLDSTRING='#@reboot root docker run -d --rm --privileged --net=host wifi:1.0'
NEWSTRING='@reboot root docker run -d --rm --privileged --net=host wifi:1.0'
FILE="/tmp/crontab"
grep -q "$NEWSTRING" $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE

# change ssid and password

OLDSTRING=$(grep ssid /tmp/wpa_supplicant/wpa_supplicant.conf)
NEWSTRING="ssid=none"
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE
	
OLDSTRING=$(grep psk /tmp/wpa_supplicant/wpa_supplicant.conf)
NEWSTRING="psk=none"
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE