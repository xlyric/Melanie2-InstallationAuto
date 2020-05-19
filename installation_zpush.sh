#!/bin/bash
HOME=$(pwd)
WEBDIR="/var/www/html"

##ajout du dépot officiel deb10
apt-get -y install wget

echo " deb https://repo.z-hub.io/z-push:/final/Debian_10/ / " >>/etc/apt/sources.list
wget -qO - http://repo.z-hub.io/z-push:/final/Debian_10/Release.key | sudo apt-key add -


cd $WEBDIR
#tar -xvf $HOME/z-push-2.2.9.tar.gz
#mv z-push-2.2.9 z-push
chown -R  www-data. z-push

mkdir /var/lib/z-push/
chown -R  www-data. /var/lib/z-push/

mkdir /var/log/z-push/
chown -R  www-data. /var/log/z-push/


apt-get install z-push-common  z-push-config-apache  z-push-backend-caldav  z-push-backend-imap  z-push-backend-ldap z-push-ipc-memcached 


