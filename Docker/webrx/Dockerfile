FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@fridata.de

RUN apt-get update
RUN apt-get install -y libusb-1.0-0-dev pkg-config ca-certificates\
	libsox-fmt-mp3 git-core cmake build-essential sox socat --no-install-recommends\
	nmap python2.7 libfftw3-dev vim netcat

WORKDIR /tmp

RUN echo 'blacklist dvb_usb_rtl28xxu' > /etc/modprobe.d/raspi-blacklist.conf && \
    git clone git://git.osmocom.org/rtl-sdr.git && \
    mkdir rtl-sdr/build && \
    cd rtl-sdr/build && \
    cmake ../ -DINSTALL_UDEV_RULES=ON -DDETACH_KERNEL_DRIVER=ON && \
    make && \
    make install && \
    ldconfig && \
    rm -rf /tmp/rtl-sdr

WORKDIR /tmp

RUN git clone https://github.com/simonyiszk/csdr.git && \
    cd csdr && \
    make && \
    make install && \
    cd / && \
    rm -rf /tmp/csdr
WORKDIR /opt

RUN git clone https://github.com/simonyiszk/openwebrx.git && \
    cd openwebrx

EXPOSE 8073 8888 4951 

CMD python2.7 /opt/openwebrx/openwebrx.py

