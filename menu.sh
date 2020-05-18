#!/bin/bash

echo " menu d'installation de la MCE" 

GREEN='\033[0;32m' 
RED='\033[1;31m' 
YELLOW='\033[0;33m'
BLUE='\033[1;34m'
NC='\033[0m' # No Color


############### menu
until [ "$menu" -eq "100" ] 
do 
echo -e " ${BLUE} Menu :${NC} 

${GREEN}###### installation apache php  ###### ${NC} 
0 : installation d un apache / php 5  
1 : installation d un apache / php 7
${GREEN}####### installation Nginx ######${NC} 
10 : installation d un nginx / php 5
11 : installation d un nginx / php 7
${GREEN}####### installation postgres ######${NC}
20 : Installation Postgres 


${GREEN}####### installation OBM ######${NC}
40 : Installation OBM
${GREEN}####### installation Roundcube ######${NC}
50 : Installation Roundcube

100 : quit 
"

echo -e " ${YELLOW} entrer  votre choix : ${NC}"
read menu 

############### case of
case $menu in 

	0 ) ### creation d'apache php5 
		./installation_apache_php5.sh
	;;

        1 ) ### creation d'apache php7
                ./installation_apache_php7.sh
        ;;


        10 ) ### creation d'Nginx php5
                ./installation_nginx_php5.sh
        ;;

        11 ) ### creation d'Nginx php7
		./installation_nginx_php7.sh
        ;;


        20 ) ### creation d'Nginx php5
                ./installation_postgres.sh
        ;;


        40 ) ### Installation ORM
                ./installation_ORM.sh
        ;;

        50 ) ### Installation Roudncube
                ./installation_Roundcube.sh
        ;;



esac
done


