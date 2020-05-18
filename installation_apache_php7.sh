#!/bin/bash

###
apt-get install -y apache2  php7.0 php7.0-fpm php7.0-mcrypt php7.0-memcached php7.0-mbstring memcached apache2-mpm-event

#conf  php avec Nginx
cp test.php /var/www/html/
a2dismod mpm_prefork
a2enmod mpm_event
a2enmod proxy_fcgi
service apache2 restart

# conf memcache
cp memcached.conf /etc/memcached.conf
service memcached restart
