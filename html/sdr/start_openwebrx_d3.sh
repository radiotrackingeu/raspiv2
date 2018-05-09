#!/bin/bash

# first copy config

cp /cfiles/config_webrx_d3.py /opt/openwebrx/newconfig.py

# start openwebrx with modified config

cd /opt/openwebrx/

python2.7 openwebrx.py newconfig