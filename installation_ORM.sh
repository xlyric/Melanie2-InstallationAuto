#!/bin/bash

#### installationd de L'ORM 

HOME=$(pwd)
WEBFOLDER="/var/www/html"
ORMFOLDER="/etc/ORM-M2"
PHPCONFIG="/etc/php5/fpm/php.ini"

### récupération de la source 

apt-get install -y git
mkdir $ORMFOLDER
chown www-data. $ORMFOLDER
cd $ORMFOLDER/..
git clone https://github.com/messagerie-melanie2/ORM-M2.git

echo "include_path = \".:/usr/share/php:$ORMFOLDER\"" >>  $PHPCONFIG

service php5-fpm reload

mkdir /etc/LibM2/
chown www-data. $ORMFOLDER
cp $ORMFOLDER/config/default/* /etc/LibM2/

