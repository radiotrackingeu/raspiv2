#!/bin/bash

# first Argument is the new hostname
OLDSTRING=$(</tmp/hostname)
echo "old hostname is $OLDSTRING"
NEWSTRING="$1"
FILE="/tmp/hosts"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE
	
FILE="/tmp/hostname"
grep -q $OLDSTRING $FILE && 
    sed -i "s/$OLDSTRING/$NEWSTRING/g" $FILE
	
echo "changed hostname to $NEWSTRING"