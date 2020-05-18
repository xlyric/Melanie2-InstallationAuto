#!/bin/bash

GREEN='\033[0;32m'
RED='\033[1;31m'
YELLOW='\033[0;33m'
BLUE='\033[1;34m'
NC='\033[0m' # No Color


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

apt-get install -y php5-pgsql php5-dom  
apt-get install -y php-dom php-pgsql

wget  https://github.com/messagerie-melanie2/Roundcube-Mel/releases/download/1.4.7.4/Roundcube_Mel_1.4.7.4_ORM_0.5.0.11_20190711115552.tar.gz  
mv Roundcube_Mel_1.4.7.4_ORM_0.5.0.11_20190711115552.tar.gz Roundcubemel.tar.gz

cd $WEBFOLDER
tar -xvf $HOME/Roundcubemel.tar.gz

mkdir /var/www/html/webmail/logs/
chown -R www-data. /var/www/html/webmail

mkdir /etc/LibM2/
chown -R www-data.  /etc/LibM2/
cp  $WEBFOLDER//webmail/vendor/messagerie-melanie2/ORM-M2/config/default/* /etc/LibM2/

cp  $HOME/config.inc.roundcube /var/www/html/webmail/config/config.inc.php
chown -R www-data. /var/www/html/webmail/config/config.inc.php 

cp $HOME/*.sql /tmp

echo -e " ${RED}commandes à passer : ${NC}" 
echo -e "${GREEN}psql -U postgres -f /tmp/Roundcube_init_base.sql"
echo "psql -d roundcube -U postgres -f /tmp/roundcube.initial.sql" 
echo -e "et rajouter les droit 'alter role roundcube superuser;' ${NC} " 

