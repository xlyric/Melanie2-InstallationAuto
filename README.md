
# Package Installations de Melanie2 

## Description

Ce dépot git contient une aide à l'installation de Mélanie 2. 
le choix initiale était de faire des scripts pour l'installation puis pour des raisons de simplicité lors de déploiement type Ansible ou autre, la création de fichier .deb est le but.
Le répertoire deb contient donc les sources de créations et le générateur de packet.

Les packets sont donc à générer avec la dernière version de l'ORM et des sources initiales de Mélanie. 
et peuvent ensuite être utilisé en local ou à distance. 
L'installation est faite pour Debian 10

## Installation 

la source est à récuperer sur la machine Debian 10 

```bash
wget https://github.com/xlyric/Melanie2-InstallationAuto/archive/master.zip
unzip master.zip
cd Melanie2-InstallationAuto/deb/
./generation_deb.sh
```

les packets Debian seront alors construits. 

il ne reste plus qu'a lancer l'installation souhaitée ex pour roundcube
```bash
apt install ./install_roundcube_M2.deb
```


## Présentation des packages :

install_orm : installe l'orm uniquement. la configuration reste à faire
install_nginx : Installe un Nginx avec support php7
install_apache2 : Installe un apache avec support php7
install_pegase : installe un nginx avec l'application pegase
install_roundcube : installe un nginx avec un roundcube
install_roundcube_M2 : installe un nginx avec un roundcube, l'ORM et les plugins Melanie2
install_proxy: installe in Nginx en mode proxy pour être connecté aux applications Melanie
install_postgres4php : installe postgres et le support PHP
install_sabredav : installe sabredav
install_sabredav_M2 : installe la version melanie 2 de sabredav
install_z-push : installe l'application zpush et la base de donnée
install_z-push-onlyweb : installe uniquement le site zpush
install_z-push-postgres : installe le postgres pour zpush

generation_deb.sh : génère les packages debian à partir des sources


