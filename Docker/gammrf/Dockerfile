# ΓRF client
FROM ubuntu:xenial

RUN apt-get update
RUN apt-get install -y wget git build-essential cmake gpsd gpsd-clients libusb-1.0-0-dev
RUN apt-get install -y vim librtlsdr-dev python-pip python-dev pkg-config libfftw3-dev

# hackrf
RUN cd /tmp; wget https://github.com/mossmann/hackrf/releases/download/v2017.02.1/hackrf-2017.02.1.tar.xz
RUN cd /tmp; tar xf hackrf-2017.02.1.tar.xz; cd hackrf-2017.02.1/host; mkdir build
ADD ./hackrf_sweep.patch /tmp/hackrf_sweep.patch
RUN cd /tmp/hackrf-2017.02.1/host/hackrf-tools/src; patch < /tmp/hackrf_sweep.patch
RUN cd /tmp/hackrf-2017.02.1/host/build; cmake ..; make; make install; ldconfig

# rtl-sdr
RUN cd /tmp; git clone https://github.com/keenerd/rtl-sdr
RUN cd /tmp/rtl-sdr; mkdir build; cd build; cmake ..; make; make install

ADD ./requirements.txt /requirements.txt
RUN pip install --upgrade pip
RUN pip install -r /requirements.txt

ADD ./gammarf.conf /gammarf.conf
ADD ./gammarf.py /gammarf.py
ADD ./modules /modules
RUN chmod +x /gammarf.py
