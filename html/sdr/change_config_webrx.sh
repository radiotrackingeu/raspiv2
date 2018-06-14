#!/bin/bash
#$1: device id
#$2: fft_fps
#$3: fft_size
#$4: sample rate
#$5: center frequency
#$6: gain

FILE="/var/www/html/sdr/config_webrx_d$1.py"

#$2 is the fft_fps
OLDSTRING="fft_fps"
NEWSTRING="fft_fps=$2"
grep -q "^$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\\$NEWSTRING" $FILE
	
echo "Set FFT Framerate to $2"
	
#$3 is the fft_size
OLDSTRING="fft_size"
NEWSTRING="fft_size=$3"
grep -q "^$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\\$NEWSTRING" $FILE

echo "Set FFT Size to $3"
	
#$4 is the sample rate

OLDSTRING="samp_rate"
NEWSTRING="samp_rate=$4"
grep -q "^$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\\$NEWSTRING" $FILE
	
echo "Set sample rate to $4"
	
#$5 is the center frequency

OLDSTRING="center_freq"
NEWSTRING="center_freq=$5"
grep -q "^$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\\$NEWSTRING" $FILE
	
echo "Set center frequency to $5"
	
#$6 is the gain

OLDSTRING="rf_gain"
NEWSTRING="rf_gain=$6"
FILE="/var/www/html/sdr/config_webrx_d$1.py"
grep -q "$OLDSTRING" $FILE && 
    sed -i "/^$OLDSTRING/c\\$NEWSTRING" $FILE
	
echo "Set gain to $6"