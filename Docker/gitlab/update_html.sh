#!/bin/bash
cp /var/www/html/git/id_rsa /root/.ssh/id_rsa
cp /var/www/html/git/id_rsa.pub /root/.ssh/id_rsa.pub
chown 600 /root/.ssh/id_rsa
git config --global user.email "publicuser@radio-tracking.eu" && \
git config --global user.name "Radio Tracking Eu"
eval $(ssh-agent -s) && ssh-add /root/.ssh/id_rsa
cd /home/pi/gitrep/radio-tracking.eu_v2/
git pull
cp -R /home/pi/gitrep/radio-tracking.eu_v2/html/ /var/www/