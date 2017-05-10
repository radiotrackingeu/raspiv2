FROM rtlsdr

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN apt-get update && \
    apt-get install -y install libtool libusb-1.0.0-dev librtlsdr-dev rtl-sdr

RUN mkdir rtl_433 && \
	cd rtl_433 && \
	git clone https://github.com/merbanan/rtl_433.git && \
	cd rtl_433 && \
    mkdir build && \
	cd build && \
	cmake ../ && \
	make && \
	make install 

WORKDIR /