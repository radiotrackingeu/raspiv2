FROM resin/rpi-raspbian:jessie

MAINTAINER ralf.zeidler@radio-tracking.eu


RUN apt-get update && \
    apt-get install -y apache2 apache2-doc apache2-utils sqlite3 \
					php5 php5-mysql  libapache2-mod-php5 php5 php-pear php5-xcache phpmyadmin 


ARG PACKAGE_URL=https://repo.mysql.com/yum/mysql-8.0-community/docker/x86_64/mysql-community-server-minimal-8.0.2-0.1.dmr.el7.x86_64.rpm
ARG PACKAGE_URL_SHELL=https://repo.mysql.com/yum/mysql-tools-community/el/7/x86_64/mysql-shell-1.0.9-1.el7.x86_64.rpm

# Install server
RUN rpmkeys --import https://repo.mysql.com/RPM-GPG-KEY-mysql \
  && yum install -y $PACKAGE_URL $PACKAGE_URL_SHELL libpwquality \
  && yum clean all \
  && mkdir /docker-entrypoint-initdb.d

VOLUME /var/lib/mysql

COPY docker-entrypoint.sh /entrypoint.sh
COPY healthcheck.sh /healthcheck.sh
ENTRYPOINT ["/entrypoint.sh"]
HEALTHCHECK CMD /healthcheck.sh
EXPOSE 3306 33060
CMD ["mysqld"]