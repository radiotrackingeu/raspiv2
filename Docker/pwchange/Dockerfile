FROM alpine:3.7

MAINTAINER Philipp Mallot

RUN apk update && apk add --no-cache \
#	vim \
	apache2-utils 

COPY pwchange.sh /pwchange.sh
RUN chmod +x /pwchange.sh