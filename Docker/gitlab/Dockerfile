FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN apt-get update && \
    apt-get install git ssh zip

RUN mkdir /root/.ssh/
	
RUN sed -i 's/#   StrictHostKeyChecking ask/   StrictHostKeyChecking no/g' /etc/ssh/ssh_config
RUN sed -i "\$aLogLevel=quiet" /etc/ssh/ssh_config
