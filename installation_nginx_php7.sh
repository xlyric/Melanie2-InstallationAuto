#!/bin/bash

###
apt-get install -y nginx  php-fpm  php-memcached memcached php

##"
#apt-get install -y php-imagick php-xml php5-zip php5-ldap php-gd php-intl

#conf  php avec Nginx
cp test.php /var/www/html/
cp nginx_default_php7 /etc/nginx/sites-enabled/default
service nginx restart

# conf memcache
cp memcached.conf /etc/memcached.conf
service memcached restart
