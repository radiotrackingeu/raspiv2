FROM rtlsdr

MAINTAINER ralf.zeidler@radio-tracking.eu

#RUN apt-get update && \
#    apt-get install 
	
RUN mkdir liquidsdr && \
	cd liquidsdr && \
	git clone https://github.com/jgaeddert/liquid-dsp.git && \
	cd liquid-dsp && \
	./bootstrap.sh && \
	./configure && \
	make && \
	make install 

