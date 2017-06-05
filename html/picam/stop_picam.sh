#!/bin/bash

#Deactivate Picam

#Change /boot/config.txt

OLDSTRING="start_x=1"
NEWSTRING="start_x=0"
FILE="/tmp1/config.txt"
grep -q "start_x" $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE
	
	
OLDSTRING="gpu_mem=256"
NEWSTRING="gpu_mem=16"
FILE="/tmp1/config.txt"
grep -q "gpu_mem" $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

	
OLDSTRING="bcm2835-v4l2"
NEWSTRING="#bcm2835-v4l2"
FILE="/tmp2/modules"
grep -q $OLDSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE