#!/bin/bash

# Change Hotspot name aka SSID in hostapd.conf

# Change Hotspot Password in hostapd.conf

# Change IP-Range, DNS Server and static adress dnsmasq.conf and interfaces





# Modify /etc/dhcpcd.conf

sysctl -w net.ipv4.ip_forward=1
/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -t nat -F POSTROUTING
/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
