FROM resin/rpi-raspbian:jessie

RUN apt-get update && apt-get install sox

ENV file nofilenamegiven

CMD play $file repeat 100000