#!/bin/bash

#&1 is the fft_size

OLDSTRING='fft_size'
NEWSTRING='fft_size=$1'
FILE="/var/www/html/sdr/config_webrx.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "s|^$OLDSTRING|$NEWSTRING|g" $FILE