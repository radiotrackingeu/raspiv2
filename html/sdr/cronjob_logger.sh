#!/bin/bash

OLDSTRING=$1
NEWSTRING=$2

FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
	sed -i "s|$OLDSTRING|$NEWSTRING|g" $FILE || echo "$NEWSTRING \n#" >> $FILE
	
echo $OLDSTRING
echo $NEWSTRING