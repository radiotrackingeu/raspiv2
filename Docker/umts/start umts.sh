#!/bin/bash

usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' &&\
sudo modprobe option
echo "12d1 1001" | sudo tee /sys/bus/usb-serial/drivers/option1/new_id

#sysctl -w net.ipv4.ip_forward=1
#/sbin/iptables -A FORWARD -o eth0 -i wlan0 -m conntrack --ctstate NEW -j ACCEPT
#/sbin/iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
#/sbin/iptables -t nat -F POSTROUTING
#/sbin/iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE


wvdial & disown
openvpn /etc/openvpn/client.conf

/bin/bash



#sudo ./sakis3g connect

#sudo docker run -it --net="host" --privileged umts bash
#sudo docker run --privileged --net=host -it -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts

#sudo ./home/modem3g/sakis3g connect USBMODEM="12d1:1001"
#sudo ./home/modem3g/sakis3g connect APN="web.vodafone.de"

#sudo service openvpn start



#Server portforwarding
#http://unix.stackexchange.com/questions/55791/port-forward-to-vpn-client

#sysctl -w net.ipv4.ip_forward=1
#iptables -t nat -A PREROUTING -d x.x.x.x -p tcp --dport 6000 -j DNAT --to-dest y.y.y.100:6000
#iptables -t nat -A POSTROUTING -d y.y.y.100 -p tcp --dport 6000 -j SNAT --to-source y.y.y.1

#Client
#/sbin/ip addr show ppp0 | grep peer | awk ' { print $4 } ' | sed 's/\/32//'

#ip route replace default via xx:xx:xx:xx dev ppp0

#ip route replace default via $(/sbin/ip addr show ppp0 | grep peer | awk ' { print $4 } ' | sed 's/\/32//') dev ppp0


