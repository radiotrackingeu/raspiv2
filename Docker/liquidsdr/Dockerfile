FROM rtlsdr

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN apt-get update && \
    apt-get install autotools-dev autoconf automake libpulse-dev libgtk-3-dev && \
					freeglut3-dev 

RUN mkdir SoapySDR && \
	cd SoapySDR && \
	git clone https://github.com/pothosware/SoapySDR.git && \
	cd SoapySDR && \
	mkdir build && \
	cd build && \
	cmake ../ -DCMAKE_BUILD_TYPE=Release && \
	make -j4 && \
	make install && \
	ldconfig && \
	SoapySDRUtil --info && \
	cd ..
	

RUN mkdir liquidsdr && \
	cd liquidsdr && \
	git clone https://github.com/jgaeddert/liquid-dsp.git && \
	cd liquid-dsp && \
	./bootstrap.sh && \
	CFLAGS="-march=native -O3" ./configure --enable-fftoverride && \
	make  -j4 && \
	make install && \
	ldconfig && \
	cd ..

RUN mkdir wxWidgest && \
	cd wxWidgest && \
	wget https://github.com/wxWidgets/wxWidgets/releases/download/v3.1.0/wxWidgets-3.1.0.tar.bz2 && \
	tar -xvjf wxWidgets-3.1.0.tar.bz2  && \
	cd wxWidgets-3.1.0/ && \
	mkdir -p ~/Develop/wxWidgets-staticlib && \
	/autogen.sh && \
	./configure --with-opengl --disable-shared --enable-monolithic --with-libjpeg --with-libtiff --with-libpng --with-zlib --disable-sdltest --enable-unicode --enable-display --enable-propgrid --disable-webkit --disable-webview --disable-webviewwebkit --prefix=`echo ~/Develop/wxWidgets-staticlib` CXXFLAGS="-std=c++0x" && \
	wxWidgets-3.1.0$ make -j4 && \
	make install && \
	cd ..
	
RUN mkdir CubicSDR && \
	cd CubicSDR && \
	git clone https://github.com/cjcliffe/CubicSDR.git && \
	cd CubicSDR && \
	mkdir build && \
	cd build && \
	cmake ../ -DCMAKE_BUILD_TYPE=Release -DwxWidgets_CONFIG_EXECUTABLE=~/Develop/wxWidgets-staticlib/bin/wx-config && \
	make && \
	cd x64/ && \
	./CubicSDR && \
	make install && \
	
	