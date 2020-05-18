#!/bin/bash

#### installationd de L'ORM 

PHPVERSION=$(./check_php.sh)

HOME=$(pwd)
WEBFOLDER="/var/www/html"
ORMFOLDER="/etc/ORM-M2"
PHPCONFIG="/etc/php5/fpm/php.ini"

### récupération de la source 
apt-get install -y wget

wget https://github.com/messagerie-melanie2/SabreDAVM2/releases/download/0.6.7/SabreDAVMel_0.6.7_ORM_0.5.0.12_20191014140152.tar.gz 
mv SabreDAVMel_0.6.7_ORM_0.5.0.12_20191014140152.tar.gz SabreDAVmel.tar.gz

cd $WEBFOLDER
tar -xvf $HOME/SabreDAVmel.tar.gz

mkdir  /var/log/sabredav
chown www-data. /var/log/sabredav

