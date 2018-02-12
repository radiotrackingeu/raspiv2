FROM resin/rpi-raspbian:jessie

MAINTAINER radiotrackingeu <info@radio-tracking.eu>

RUN echo deb http://mirrordirector.raspbian.org/raspbian/ stable main contrib non-free rpi >> /etc/apt/sources.list

RUN apt-get update
RUN apt-get install -y apt-utils
RUN apt-get install -y ffmpeg
RUN apt-get install -y motion
COPY ./motion.conf /etc/motion/motion.conf

CMD motion