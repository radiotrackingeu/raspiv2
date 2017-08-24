#!/bin/bash

#$1 is the fft_fps

OLDSTRING="fft_fps"
NEWSTRING="fft_fps=$1"
FILE="/var/www/html/sdr/config_webrx.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\$NEWSTRING" $FILE
	
echo "Set FFT Framerate to $1"
	
#$2 is the fft_size

OLDSTRING="fft_size"
NEWSTRING="fft_size=$2"
FILE="/var/www/html/sdr/config_webrx.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\$NEWSTRING" $FILE

echo "Set FFT Size to $2"
	
#$3 is the fft_size

OLDSTRING="samp_rate"
NEWSTRING="samp_rate=$3"
FILE="/var/www/html/sdr/config_webrx.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\$NEWSTRING" $FILE
	
echo "Set sample rate to $3"
	
#$4 is the fft_size

OLDSTRING="center_freq"
NEWSTRING="center_freq=$4"
FILE="/var/www/html/sdr/config_webrx.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\$NEWSTRING" $FILE
	
echo "Set center frequency to $4"
	
#$5 is the fft_size

OLDSTRING="rf_gain"
NEWSTRING="rf_gain=$5"
FILE="/var/www/html/sdr/config_webrx.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\$NEWSTRING" $FILE
	
echo "Set gain to $5"
	
	
	
	
	