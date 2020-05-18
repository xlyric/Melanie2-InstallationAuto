#!/bin/bash

#### installationd de L'ORM 

HOME=$(pwd)
WEBFOLDER="/var/www/html"
ORMFOLDER="/etc/ORM-M2"
PHPVERSION=$(./check_php.sh)
PHPCONFIG5="/etc/php5/fpm/php.ini"
PHPCONFIG7="/etc/php/7.3/fpm/php.ini"
### récupération de la source 

apt-get install -y git
mkdir $ORMFOLDER
chown www-data. $ORMFOLDER
cd $ORMFOLDER/..
git clone https://github.com/messagerie-melanie2/ORM-M2.git

if [ $PHPVERSION -eq "5" ]
then
echo "include_path = \".:/usr/share/php:$ORMFOLDER\"" >>  $PHPCONFIG5
service php5-fpm reload

else
echo "include_path = \".:/usr/share/php:$ORMFOLDER\"" >>  $PHPCONFIG7
service php7.3-fpm reload
fi


mkdir /etc/LibM2/
chown www-data. $ORMFOLDER
cp $ORMFOLDER/config/default/* /etc/LibM2/

