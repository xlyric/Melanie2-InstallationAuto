#!/bin/bash

###
apt-get install -y apache2  php5-fpm php5-mcrypt php5-memcached memcached php5 apache2-mpm-event

#conf  php avec Nginx
cp test.php /var/www/html/
a2dismod mpm_prefork
a2enmod mpm_event
a2enmod proxy_fcgi
service apache2 restart

# conf memcache
cp memcached.conf /etc/memcached.conf
service memcached restart
