#!/bin/bash

###
PHPVERSION=$(./check_php.sh)


if [ $PHPVERSION -eq "7" ]
then
apt-get install -y postgresql postgresql-client postgresql-contrib
else
apt-get install -y postgresql-9.4 postgresql-client-9.4 postgresql-contrib

fi

apt-get install -y php-dom  php-pgsql
