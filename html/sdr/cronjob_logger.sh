#!/bin/bash

#

OLDSTRING="$1"
NEWSTRING="$2"
echo $NEWSTRING;
FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
	sed -i "/$OLDSTRING/c\\$NEWSTRING" $FILE || echo -e "$NEWSTRING \n#" >> $FILE
	
