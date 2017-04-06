#!/bin/bash

sysctl -w net.ipv4.ip_forward=1
rfkill block wifi
rfkill unblock wifi
ifup wlan0
/etc/init.d/dnsmasq start
/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -t nat -F POSTROUTING
/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
sudo hostapd -d /etc/hostapd/hostapd.conf