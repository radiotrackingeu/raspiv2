FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@fridata.de

RUN apt-get update
RUN apt-get install -y libusb-1.0-0-dev pkg-config ca-certificates\
	libsox-fmt-mp3 git-core cmake build-essential sox socat --no-install-recommends\
	nmap python2.7 libfftw3-dev vim

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
ENV commit_id 0fd32aabbd56d3d5d11f828b933d85dab62d5680 

RUN git clone https://github.com/simonyiszk/csdr.git && \
    cd csdr && \
    git reset --hard $commit_id && \
    make && \
    make install && \
    cd / && \
    rm -rf /tmp/csdr
WORKDIR /opt
ENV commit_id 6b06d13a934a093064f231f37bdafee6a3cc3b1c 
ENV branch master 

RUN git clone https://github.com/simonyiszk/openwebrx.git && \
    cd openwebrx && \
    git checkout $branch && \
    git reset --hard $commit_id
WORKDIR /opt/openwebrx

RUN sed -i "s/center_freq = 145525000/center_freq = 150150000/g" /opt/openwebrx/config_webrx.py
RUN sed -i "s/rf_gain = 5/rf_gain = 20/g" /opt/openwebrx/config_webrx.py




EXPOSE 8073 8888 4951 

CMD python2.7 openwebrx.py

