#!/bin/bash

# change /etc/crontab and uncomment docker wifi line
OLDSTRING="@reboot root docker run -d --rm --privileged --net=host wifi"
NEWSTRING="#@reboot root docker run -d --rm --privileged --net=host wifi"
FILE="/tmp/crontab"
grep -q '$OLDSTRING' $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n #" >> $FILE

# change interfaces and comment wpa_supllicant line - if no connection possible start as hotspot again
