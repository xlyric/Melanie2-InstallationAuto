#!/bin/bash

###
apt-get update
apt-get install -y apt-transport-https ca-certificates curl gnupg2 software-properties-common

# installation de docker

curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -
add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable"
apt update

apt install docker-ce
systemctl status docker
