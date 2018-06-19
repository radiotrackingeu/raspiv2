#!/bin/bash
# $1: SSID
# $2: PW

# change ssid and password

OLDSTRING="^ssid.*"
NEWSTRING="ssid=\"$1\""
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE
	
OLDSTRING="^psk.*"
NEWSTRING="psk=\"$2\""
FILE="/tmp/wpa_supplicant/wpa_supplicant.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE