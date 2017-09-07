FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@radio-tracking.eu

RUN { \
		echo mysql-server mysql-server/data-dir select ''; \
		echo mysql-server mysql-server/root-pass password ''; \
		echo mysql-server mysql-server/re-root-pass password ''; \
		echo mysql-server mysql-server/remove-test-db select false; \
	} | debconf-set-selections 

RUN apt-get update && \
    apt-get install -y apache2 apache2-doc apache2-utils mysql-server mysql-client sqlite3 \
					php5 php5-mysql  libapache2-mod-php5 php5 php-pear php5-xcache phpmyadmin php5-sqlite3
	

