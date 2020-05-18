#!/bin/bash

###
PHPVER=$(ps faux |grep php-fpm | grep 7.3 | wc -l)

if [ $PHPVER -eq 1 ]
then
apt-get install -y postgresql postgresql-client postgresql-contrib
else
apt-get install -y postgresql-9.4 postgresql-client-9.4 postgresql-contrib
fi
