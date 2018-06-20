#!/bin/bash


OLDSTRING="$cfg['Servers'][$i]['host'] = 'extern1';"
NEWSTRING="$cfg['Servers'][$i]['host'] = '$1';"
FILE="/etc/phpmyadmin/config.ini.php"
grep -q "$OLDSTRING" $FILE && 
	sed -i "s|.*$OLDSTRING.*|$NEWSTRING|" $FILE
	
	
	
	