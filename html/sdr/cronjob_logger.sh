#!/bin/bash

OLDSTRING="$1"
NEWSTRING="$2"

FILE="/tmp/crontab"
grep -q "$OLDSTRING" $FILE && 
	sed -i "/*\$OLDSTRING*/c\\$NEWSTRING" $FILE || echo "$NEWSTRING \n#" >> $FILE
	
