#!/bin/bash

#### installationd de L'ORM 
PHPVERSION=$(./check_php.sh)



HOME=$(pwd)
WEBFOLDER="/var/www/html"
ORMFOLDER="/etc/ORM-M2"

### récupération de la source 
apt-get install -y wget

if [ $PHPVERSION -eq "5" ]
then
apt-get install -y  php5-dom
service php5-fpm reload
else
apt-get install -y php-dom 
service php7.3-fpm reload
fi


wget https://github.com/messagerie-melanie2/SabreDAVM2/releases/download/0.6.7/SabreDAVMel_0.6.7_ORM_0.5.0.12_20191014140152.tar.gz 
mv SabreDAVMel_0.6.7_ORM_0.5.0.12_20191014140152.tar.gz SabreDAVmel.tar.gz

cd $WEBFOLDER
tar -xvf $HOME/SabreDAVmel.tar.gz

mkdir  /var/log/sabredav
chown www-data. /var/log/sabredav

mkdir /etc/LibM2/

cp  $WEBFOLDER/davm2/vendor/messagerie-melanie2/ORM-M2/config/default/* /etc/LibM2/
chown -R www-data.  /etc/LibM2/

