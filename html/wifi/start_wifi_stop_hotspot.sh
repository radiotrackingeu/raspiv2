#!/bin/bash

# change /etc/crontab and comment docker wifi line
OLDSTRING=$(grep -P 'wifi:\d\.\d' $FILE)
NEWSTRING='#@reboot root docker run -d --name=wifi --rm --privileged --net=host -v /var/www/html/wifi/hostapd.conf:/etc/hostapd/hostapd.conf wifi:1.0'
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE

ssids=('UPC5692673','rteu_access' )
connected=false
for ssid in "${ssids[@]}"
do
    echo " "
    echo "checking if ssid available:" $ssid
    echo " "
    if iwlist wlan0 scan | grep $ssid > /dev/null
    then
        echo "First WiFi in range has SSID:" $ssid
        echo "Starting supplicant for WPA/WPA2"
        wpa_supplicant -B -i wlan0 -c /etc/wpa_supplicant/wpa_supplicant.conf > /dev/null 2>&1
        echo "Obtaining IP from DHCP"
        if dhclient -1 wlan0
        then
            echo "Connected to WiFi"
            connected=true
            break
        else
            echo "DHCP server did not respond with an IP lease (DHCPOFFER)"
            wpa_cli terminate
            break
        fi
    else
        echo "Not in range, WiFi with SSID:" $ssid
    fi
done
 
if ! $connected; then
    createAdHocNetwork
fi
	

# Change Hotspot name aka SSID in hostapd.conf

# Change Hotspot Password in hostapd.conf

# Change IP-Range, DNS Server and static adress dnsmasq.conf and interfaces





# Modify /etc/dhcpcd.conf

sysctl -w net.ipv4.ip_forward=1
/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -t nat -F POSTROUTING
/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
