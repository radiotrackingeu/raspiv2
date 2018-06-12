#!/bin/bash
# $1: SSID
# $2: PW

# change /etc/crontab and uncomment docker wifi line
FILE="/tmp/crontab"
OLDSTRING=$(grep -P 'wifi:\d\.\d' $FILE)
NEWSTRING='@reboot root docker run -d --name=wifi --rm --privileged --net=host -v /var/www/html/wifi/hostapd.conf:/etc/hostapd/hostapd.conf wifi:1.0'
grep -q "$OLDSTRING" $FILE && 
    sed -i "s~$OLDSTRING~$NEWSTRING~g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE

# change ssid and password

FILE="/tmp1/hostapd.conf"
OLDSTRING="^ssid.*$"
NEWSTRING="ssid=$1"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE

OLDSTRING="^wpa_passphrase.*$"
NEWSTRING="wpa_passphrase=$2"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE

OLDSTRING="^ssid.*$"
NEWSTRING="ssid=none"
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE
	
OLDSTRING="^psk.*$"
NEWSTRING="psk=none"
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE