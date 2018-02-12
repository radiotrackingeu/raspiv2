FROM resin/rpi-raspbian:stretch

MAINTAINER radiotrackingeu <info@radio-tracking.eu>

RUN apt-get update
RUN apt-get install -y apt-utils
RUN apt-get install -y ffmpeg
RUN apt-get install -y motion
COPY ./motion.conf /etc/motion/motion.conf

CMD motion