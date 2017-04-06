FROM resin/rpi-raspbian:jessie

MAINTAINER Ralf Zeidler

RUN echo "deb http://mirrordirector.raspbian.org/raspbian/ stretch main contrib non-free rpi" > /etc/apt/sources.list


RUN gpg --keyserver pgpkeys.mit.edu --recv-key CCD91D6111A06851 && \
    gpg --armor --export CCD91D6111A06851 | sudo apt-key add -

RUN apt-get update && apt-get install php7.0 \
    php7.0-curl php7.0-gd php7.0-imap php7.0-json \
    php7.0-mcrypt php7.0-mysql php7.0-opcache php7.0-xmlrpc

RUN apt-get install apache2 libapache2-mod-php7.0

RUN service apache2 start

EXPOSE 80