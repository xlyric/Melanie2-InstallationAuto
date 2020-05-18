#!/bin/bash
HOME=$(pwd)
WEBDIR="/var/www/html"

###
#STATE=$(service postgresql status | grep enabled | wc -l ) 
## vérification de la présence de postgres
#if [  $STATE -eq '0' ] 
#then
#./installation_postgres.sh
#fi 



STATE=$(service nginx status | grep running | wc -l )
## vérification de la présence de nginx
if [  $STATE -eq '0'  ]
then
./installation_nginx_php.sh
fi

cd $WEBDIR
#tar -xvf $HOME/z-push-2.2.9.tar.gz
#mv z-push-2.2.9 z-push
chown -R  www-data. z-push

mkdir /var/lib/z-push/
chown -R  www-data. /var/lib/z-push/

mkdir /var/log/z-push/
chown -R  www-data. /var/log/z-push/


