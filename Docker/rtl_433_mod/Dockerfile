FROM rtl_433

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN rm rtl_433 && \
	mkdir rtl_433 && \
	cd rtl_433 && \
	git clone https://github.com/radiotrackingeu/rtl_433.git && \
	cd rtl_433 && \
    mkdir build && \
	cd build && \
	cmake ../ && \
	make && \
	make install 

WORKDIR /