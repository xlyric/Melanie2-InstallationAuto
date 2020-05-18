#!/bin/bash

###
apt-get update
apt-get install -y apache2  php php-fpm php-memcached php-mbstring memcached

#conf  php avec Nginx
cp test.php /var/www/html/
a2dismod mpm_prefork
a2enmod mpm_event
a2enmod proxy_fcgi
service apache2 restart

### 
cp  000-default.conf /etc/apache2/sites-enabled/000-default.conf

# conf memcache
cp memcached.conf /etc/memcached.conf

service memcached restart
