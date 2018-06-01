FROM alpine:3.7

MAINTAINER Philipp Mallot

RUN apk update && apk add --no-cache \
#	vim \
	apache2-utils \
	mysql-client 

COPY pwchange.sh /pwchange.sh
COPY mysql_pwchange.sh /mysql_pwchange.sh
RUN chmod +x /pwchange.sh
RUN chmod +x /mysql_pwchange.sh