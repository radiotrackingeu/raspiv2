#!/bin/bash

echo "Selecting branch $1... <br>"
git -C /home/pi/gitrep/raspiv2 checkout $1
echo 'Copying files to server root... <br>'
cp -R /home/pi/gitrep/raspiv2/html/ /var/www/
echo 'Setting file permissions... <br>'
find /var/www -not -path "*/mysql*" -exec chown www-data:www-data {} \;