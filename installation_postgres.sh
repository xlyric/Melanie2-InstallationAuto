#!/bin/bash

###
PHPVER=$(ps faux |grep php-fpm | grep 7.3 | wc -l)

apt-get install -y postgresql-9.4 postgresql-client-9.4 postgresql-contrib
apt-get install -y postgresql postgresql-client postgresql-contrib

apt-get install -y php-dom  php-pgsql
