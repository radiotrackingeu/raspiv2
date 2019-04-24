#!/bin/bash

# The following code is needed if a private gitlab is used with ssh key

#cp /var/www/html/git/id_rsa /root/.ssh/id_rsa
#cp /var/www/html/git/id_rsa.pub /root/.ssh/id_rsa.pub
#chown 600 /root/.ssh/id_rsa
#git config --global user.email "publicuser@radio-tracking.eu" && \
#git config --global user.name "Radio Tracking Eu"
#eval $(ssh-agent -s) && ssh-add /root/.ssh/id_rsa

# Running on live branch with github repository
cd /home/pi/gitrep/raspiv2/
echo 'Download new repositry:'
git pull
git checkout $1
git reset --hard origin/$1
echo Switched to $1 branch.
echo '<br>Copy new repositry.'
if [ $2 = 'keepcfg' ]
then 
	echo '<br> Save old config.'
	cp /var/www/html/cfg/globalconfig /home/pi/globalconfig
fi

cp -R /home/pi/gitrep/raspiv2/html/ /var/www/
echo 'Refresh property rights. <br>'
find /var/www -not -path "*/mysql*" -exec chown www-data:www-data {} \;

if [ $2 = 'keepcfg' ]
then 
	echo '<br> Copy back old config.'
	cp /home/pi/globalconfig /var/www/html/cfg/globalconfig 
fi
