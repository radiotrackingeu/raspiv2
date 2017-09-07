FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@fridata.de

RUN apt-get update && apt-get install -y apache2 php5 libapache2-mod-php5 apt-utils php5-mysql phpmyadmin

echo "Include /etc/phpmyadmin/apache.conf" >> /etc/apache2/apache2.conf

#Change port to 8080
#Listen 80

CMD sh start_phpmyadmin.sh