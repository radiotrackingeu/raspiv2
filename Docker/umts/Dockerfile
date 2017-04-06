FROM resin/rpi-raspbian:jessie

MAINTAINER Ralf Zeidler

RUN apt-get update && \
    apt-get install ppp wvdial\
    gammu-smsd openvpn easy-rsa\
    usb-modeswitch usb-modeswitch-data\
	bzip2 net-tools usbutils

#RUN mkdir /home/modem3g

#COPY sakis3g /home/modem3g/sakis3g
#COPY wvdial.conf /etc/wvdial.conf
#COPY sdr.key /home/sdr.key
#COPY sdr.crt /home/sdr.crt
#COPY ca.crt /home/ca.crt
#COPY client.conf /etc/openvpn/client.conf 

#RUN chmod +x /home/modem3g/sakis3g

