#!/bin/bash

sudo docker stop $(sudo docker ps -a -q --filter ancestor=wifi)

sudo docker run -t --privileged --net="host" wifi sh /home/connect_wifi.sh