#!/bin/bash

cp /config/wvdial.conf /etc/wvdial.conf
wvdial 2>&1 &

while ! ifconfig | grep -F "ppp0" > /dev/null; do
    echo "wait for umts"
    sleep 1
done
sleep 1
ip route replace default via $(/sbin/ip addr show ppp0 | grep peer | awk ' { print $4 } ' | sed 's/\/32//') dev ppp0
sleep 1
openvpn /config/client.conf 2>&1