FROM rtlsdr

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN apt-get update && \
    apt-get install -y autotools-dev autoconf automake 


	

RUN mkdir liquidsdr && \
	cd liquidsdr && \
	git clone https://github.com/jgaeddert/liquid-dsp.git && \
	cd liquid-dsp && \
	./bootstrap.sh && \
	CFLAGS="-march=native -O3" && \
	./configure --enable-fftoverride && \
	make  -j4 && \
	make install && \
	ldconfig
	
#	gcc -g -O2  -ffast-math -mcpu=cortex-a7 -mfloat-abi=hard -mfpu=neon-vfpv4 -Wall -fPIC  examples/rtlsdr_signal_detect.c libliquid.a -o examples/rtlsdr_signal_detect -lfftw3f -lm -lc