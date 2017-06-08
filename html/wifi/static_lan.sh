#!/bin/bash

# Modify /etc/dhcpcd.conf

OLDSTRING="#interface eth0"
NEWSTRING="interface eth0"
FILE="/tmp2/dhcpcd.conf"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
OLDSTRING=$(sudo grep 'static ip_adress' /tmp2/dhcpcd.conf)
NEWSTRING="static ip_address=$1/24"
echo "$NEWSTRING" | grep "^##"
if [ $? -eq 0 ];then  NEWSTRING=$OLDSTRING; fi
FILE="/tmp2/dhcpcd.conf"
grep -q "static ip_address" $FILE && 
    sed -i "s|^$OLDSTRING|$NEWSTRING|g" $FILE || echo "$NEWSTRING" >> $FILE
	
OLDSTRING=$(sudo grep 'static routers' /tmp2/dhcpcd.conf)
NEWSTRING="static routers=$2"
echo "$NEWSTRING" | grep "^##"
if [ $? -eq 0 ];then  NEWSTRING=$OLDSTRING; fi
FILE="/tmp2/dhcpcd.conf"
grep -q "static routers" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
#Use google's DNS Server
	
OLDSTRING=$(sudo grep 'static domain_name_servers' /tmp2/dhcpcd.conf)
NEWSTRING="static domain_name_servers=8.8.8.8"
FILE="/tmp2/dhcpcd.conf"
grep -q "static domain_name_servers" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

sysctl -w net.ipv4.ip_forward=1
/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -t nat -F POSTROUTING
/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
