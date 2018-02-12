FROM resin/rpi-raspbian:stretch

MAINTAINER radiotrackingeu <info@radio-tracking.eu>

RUN apt-get update
RUN apt-get install -y ffmpeg
RUN apt-get install -y apt-utils motion
RUN apt-get remove --purge -y $(apt-mark showauto) && rm -rf /var/lib/apt/lists/*
COPY ./motion.conf /etc/motion/motion.conf

CMD motion