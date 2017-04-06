FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN apt-get update && \
    apt-get install -y libusb-1.0-0-dev pkg-config ca-certificates \
	git-core cmake build-essential sox socat libsox-fmt-mp3 --no-install-recommends \
	libfftw3-dev libtclap-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

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

RUN mkdir fftw && \
	cd fftw && \
	git clone https://github.com/AD-Vega/rtl-power-fftw.git && \
	cd rtl-power-fftw && \
    mkdir build && \
	cd build && \
	cmake ../ && \
	make && \
	make install 

WORKDIR /