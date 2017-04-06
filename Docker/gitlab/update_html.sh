#!/bin/bash
#cp /var/www/html/git/id_rsa /root/.ssh/id_rsa
#cp /var/www/html/git/id_rsa.pub /root/.ssh/id_rsa.pub
#chown 600 /root/.ssh/id_rsa
#git config --global user.email "publicuser@radio-tracking.eu" && \
#git config --global user.name "Radio Tracking Eu"
#eval $(ssh-agent -s) && ssh-add /root/.ssh/id_rsa
cd /home/pi/gitrep/raspiv2/
echo 'Download new repositry: <br>'
git pull
echo 'Copy new repositry. <br>'
cp -R /home/pi/gitrep/raspiv2/html/ /var/www/
echo 'Refresh porperty rights. <br>'
chown -R www-data:www-data /var/www