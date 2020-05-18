#!/bin/bash

###
apt-get install -y nginx  php5-fpm php5-mcrypt php5-memcached memcached php5

##"
#apt-get install -y php-imagick php-xml php5-zip php5-ldap php-gd php-intl

#conf  php avec Nginx
cp test.php /var/www/html/
cp default_site /etc/nginx/sites-enabled/default
service nginx restart

# conf memcache
cp memcached.conf /etc/memcached.conf
service memcached restart
