#!/bin/bash

mkdir /etc/wpa_supplicant/

echo -e "country=GB \n \
	ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev \n \
	update_config=1 \n \
	network={\n \
		ssid="HUAWEI-AF06" \n \
		psk="j9ydgbyg" \n \
		}" >/etc/wpa_supplicant/wpa_supplicant.conf 

sysctl -w net.ipv4.ip_forward=1
rfkill block wifi
rfkill unblock wifi
ifdown wlan0
ifup wlan0

#sudo docker run -d -v /home/pi/:/home/pi/ --net="host" --privileged wifi sh /home/pi/test.sh

#country=GB
#ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
#update_config=1
