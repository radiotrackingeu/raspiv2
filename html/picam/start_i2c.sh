#!/bin/bash

#Activate I2C

#Change /boot/config.txt

#Change dtparam=i2c1=on and dtparam=i2c_arm=on

OLDSTRING="#dtparam=i2c1=on"
NEWSTRING="dtparam=i2c1=on"
FILE="/tmp1/config.txt"

grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/" $FILE || echo "$NEWSTRING" >> $FILE

OLDSTRING="#dtparam=i2c_arm=on"
NEWSTRING="dtparam=i2c_arm=on"
FILE="/tmp1/config.txt"

grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/" $FILE || echo "$NEWSTRING" >> $FILE

#Add the modules to /etc/modules

LINE='i2c-bcm2708'
FILE='/tmp2/modules'
grep -q "$LINE" "$FILE" || echo "$LINE" >> "$FILE"

LINE='i2c-dev'
FILE='/tmp2/modules'
grep -q "$LINE" "$FILE" || echo "$LINE" >> "$FILE"

# sudo i2cset -y 1 0x70 0x00 0xa5 sudo i2cset -y 1 0x70 0x00 0xa5

# Then you need to turn the gain up to full using: sudo i2cset -y 1 0x70 0x09 0x0f

# If you need to turn all the LEDs off: sudo i2cset -y 1 0x70 0x00 0x00