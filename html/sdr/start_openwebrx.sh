#!/bin/bash

# first copy config

cp /cfiles/config_webrx.py /opt/openwebrx/newconfig.py

# start openwebrx with modified config

python2.7 /opt/openwebrx/openwebrx.py newconfig