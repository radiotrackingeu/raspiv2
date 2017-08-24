#!/bin/bash

# first copy config

cp /cfiles/config_webrx.py /opt/openwebrx/newconfig.py

# start openwebrx with modified config

cd /opt/openwebrx/

python2.7 openwebrx.py newconfig