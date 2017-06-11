#!/bin/bash

OLDSTRING="#$1"
NEWSTRING="$1"
FILE="$2"
grep -q $NEWSTRING $FILE && 
    sed -i "s/^$OLDSTRING/$NEWSTRING/g" $FILE || echo "$NEWSTRING \n #----------------------------------" >> $FILE
