#!/bin/sh

# first copy config

cp /cfiles/config.inc.php /etc/phpmyadmin/config.inc.php

# start openwebrx with modified config

service apache2 start && bash