#!/bin/bash

# Modify /etc/dhcpcd.conf

# $1 = IP
# $2 = Route

OLDSTRING="interface eth0"
NEWSTRING="#interface eth0"
FILE="/tmp2/dhcpcd.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
echo $1
	
OLDSTRING=$(sudo grep 'static ip' /etc/dhcpcd.conf)
NEWSTRING="#$OLDSTRING"
FILE="/tmp2/dhcpcd.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
OLDSTRING=$(sudo grep 'static routers' /etc/dhcpcd.conf)
NEWSTRING="#$OLDSTRING"
FILE="/tmp2/dhcpcd.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
#Use google's DNS Server
	
OLDSTRING=$(sudo grep 'static domain_name_servers' /etc/dhcpcd.conf)
NEWSTRING="#static domain_name_servers=8.8.8.8"
FILE="/tmp2/dhcpcd.conf"
grep -q $OLDSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

sysctl -w net.ipv4.ip_forward=1
/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -t nat -F POSTROUTING
/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
