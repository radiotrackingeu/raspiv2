#!/bin/bash

# Modify /etc/dhcpcd.conf

OLDSTRING="#interface eth0"
NEWSTRING="interface eth0"
FILE="/etc/dhcpcd.conf"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
OLDSTRING="#static ip_address=192.168.178.18/24"
NEWSTRING="static ip_address=192.168.178.18/24"
FILE="/etc/dhcpcd.conf"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
OLDSTRING="#static routers=192.168.178.1"
NEWSTRING="static routers=192.168.178.1"
FILE="/etc/dhcpcd.conf"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
OLDSTRING="#static domain_name_servers=8.8.8.8"
NEWSTRING="static domain_name_servers=8.8.8.8"
FILE="/etc/dhcpcd.conf"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

sysctl -w net.ipv4.ip_forward=1
/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -t nat -F POSTROUTING
/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
