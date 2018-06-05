#!/bin/bash
# $1: SSID
# $2: PW

# change /etc/crontab and uncomment docker wifi line
OLDSTRING='#@reboot root docker run -d --rm --privileged --net=host wifi:1.0'
NEWSTRING='@reboot root docker run -d --rm --privileged --net=host -v /var/www/html/wifi/hostapd.conf:/etc/hostapd/hostapd.conf wifi:1.0'
FILE="/tmp/crontab"
grep -q "$NEWSTRING" $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE

# change ssid and password

FILE="/tmp1/hostapd.conf"
OLDSTRING=$(grep ^ssid $FILE)
NEWSTRING="ssid=$1"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE

OLDSTRING=$(grep ^wpa_passphrase $FILE)
NEWSTRING="wpa_passphrase=$2"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE

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