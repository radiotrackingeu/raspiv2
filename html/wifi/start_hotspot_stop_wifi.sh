#!/bin/bash

# change /etc/crontab and uncomment docker wifi line
# change /etc/crontab and comment docker wifi line
OLDSTRING='#@reboot root docker run -d --rm --privileged --net=host wifi'
NEWSTRING='@reboot root docker run -d --rm --privileged --net=host wifi'
FILE="/tmp/crontab"
grep -q "$NEWSTRING" $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE || echo -e "$NEWSTRING \n#" >> $FILE
