#!/bin/bash

#Activate I2C

#Change /boot/config.txt

#Change dtparam=i2c1=on and dtparam=i2c_arm=on

sed -i 's/#dtparam=i2c1=on/dtparam=i2c1=on/g' /tmp1/config.txt
sed -i 's/#dtparam=i2c_arm=on/dtparam=i2c_arm=on/g' /tmp1/config.txt


#Add the modules to /etc/modules

echo 'i2c-bcm2708' >> /tmp2/modules
echo 'i2c-dev' >> /tmp2/modules

