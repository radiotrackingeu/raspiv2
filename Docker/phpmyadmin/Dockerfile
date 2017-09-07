FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@fridata.de

RUN apt-get update && apt-get install -y apache2 php5 libapache2-mod-php5 phpmyadmin

CMD service apache2 start

EXPOSE 80