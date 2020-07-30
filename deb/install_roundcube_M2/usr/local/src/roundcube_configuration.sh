#!/bin/bash

LOCALFILES="."
REPCONF="."
WEB="."




### recherche d'argument
if [ -Z $1 ]
then
echo "configuration post installation"
echo "configuration de la connexion Melanie"
echo "nom du serveur de base Melanie2 ?"
        read "nombdd"
        sed -i 's/sgbd.test/'$nombdd'/g' $REPCONF/sql.php

##echo "port du serveur de base Melanie2 [5432]?"
	read -e -p "port du serveur de base Melanie2:" -i "5432" "nombdd"
        sed -i 's/5432/'$nombdd'/g' $REPCONF/sql.php


echo "nom de la base ?"
        read "nombdd"
        sed -i 's/mybase/'$nombdd'/g' $REPCONF/sql.php

echo "nom du user ?"
        read "nombdd"
        sed -i 's/myuser/'$nombdd'/g' $REPCONF/sql.php

echo "mdp du user ?"
        read  "nombdd"
        sed -i 's/P4ss./'$nombdd'/g' $REPCONF/sql.php


####
echo "configuration de la connexion ldap"

echo "nom du serveur de ldap ?"
        read "nombdd"
        sed -i 's/ldap.test/'$nombdd'/g' $REPCONF/ldap.php
        sed -i 's/dc=example,dc=com/ou=boites,ou=mce,ou=fr/g' $REPCONF/ldap.php
        echo "pensez à changer le base Dn de recherche dans $REPCONF/sql.php"

#### "configuration imap
echo "nom du serveur imap ?"
        read "nombdd"
        sed -i 's/serverimap/'$nombdd'/g' $WEB/config/config.inc.php

fi



