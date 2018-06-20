#!/bin/bash

# first copy config

cp /cfiles/config_webrx_d0.py /opt/openwebrx/newconfig.py

# start openwebrx with modified config

cd /opt/openwebrx/

python2.7 openwebrx.py newconfig