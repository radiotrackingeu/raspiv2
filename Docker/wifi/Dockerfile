FROM resin/rpi-raspbian:jessie

MAINTAINER Ralf Zeidler

RUN apt-get update -y && \
    apt-get install -y hostapd dnsmasq net-tools \
    rfkill iptables iw bridge-utils ifupdown udev wpasupplicant wireless-tools \
    isc-dhcp-server wget

#RUN wget -q https://git.io/voEUQ -O /tmp/raspap && bash /tmp/raspap

COPY hostapd.conf /etc/hostapd/hostapd.conf

RUN sudo chmod 600 /etc/hostapd/hostapd.conf

RUN echo "DAMON_CONF="etc/hostapd/hostapd.conf"" > /etc/default/hostapd

COPY dnsmasq.conf /etc/dnsmasq.conf
COPY interfaces /etc/network/interfaces
COPY start_wifi.sh /home/start_wifi.sh
COPY connect_wifi.sh /home/connect_wifi.sh
CMD sh /home/start_wifi.sh

