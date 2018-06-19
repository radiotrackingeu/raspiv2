#!/bin/bash
# $1: SSID
# $2: PW

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