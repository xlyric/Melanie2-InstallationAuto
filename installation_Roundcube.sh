#!/bin/bash

#### installationd de L'ORM 

HOME=$(pwd)
WEBFOLDER="/var/www/html"
ORMFOLDER="/etc/ORM-M2"
PHPCONFIG="/etc/php5/fpm/php.ini"

### récupération de la source 
apt-get install -y wget

#### installation de postgres si non existant
# vérification de la présence de postgres
STATE=$(service postgresql status | grep enabled | wc -l )
if [  $STATE -eq '0' ]
then
./installation_postgres.sh
fi

apt-get install -y php5-pgsql

wget  https://github.com/messagerie-melanie2/Roundcube-Mel/releases/download/1.4.7.4/Roundcube_Mel_1.4.7.4_ORM_0.5.0.11_20190711115552.tar.gz  
mv Roundcube_Mel_1.4.7.4_ORM_0.5.0.11_20190711115552.tar.gz Roundcubemel.tar.gz

cd $WEBFOLDER
tar -xvf $HOME/Roundcubemel.tar.gz

mkdir /var/www/html/webmail/logs/
chown -R www-data. /var/www/html/webmail



echo "commandes à passer : " 
echo "psql -U postgres -f Roundcube_init_base.sql"
echo "psql -d roundcube -U postgres -f roundcube.initial.sql" 

