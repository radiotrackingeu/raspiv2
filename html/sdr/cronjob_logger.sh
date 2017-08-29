#!/bin/bash

OLDSTRING=$1
NEWSTRING=$2

FILE=$3
grep -q "$OLDSTRING" $FILE && 
	sed -i "s|.*$OLDSTRING.*|$NEWSTRING|" $FILE || echo "$NEWSTRING \n#" >> $FILE
	
