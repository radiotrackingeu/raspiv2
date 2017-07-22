#!/bin/bash

#Activate Hotspot and delete SSID in wpaconfig

OLDSTRING="#sudo docker run -d --rm --privileged --net=host wifi"
NEWSTRING="sudo docker run -d --rm --privileged --net=host wifi"
FILE="/tmp1/config.txt"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

OLDSTRING="#dtparam=i2c_arm=on"
NEWSTRING="dtparam=i2c_arm=on"
FILE="/tmp1/config.txt"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

#Add the modules to /etc/modules

OLDSTRING="#i2c-bcm2708"
NEWSTRING="i2c-bcm2708"
FILE="/tmp2/modules"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

OLDSTRING="#i2c-dev"
NEWSTRING="i2c-dev"
FILE="/tmp2/modules"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING" >> $FILE

# sudo i2cset -y 1 0x70 0x00 0xa5 sudo i2cset -y 1 0x70 0x00 0xa5

# Then you need to turn the gain up to full using: sudo i2cset -y 1 0x70 0x09 0x0f

# If you need to turn all the LEDs off: sudo i2cset -y 1 0x70 0x00 0x00