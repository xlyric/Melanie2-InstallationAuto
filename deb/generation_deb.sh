#!/bin/bash

echo "regénération des fichiers deb"

ls -d */ | cut -d"/" -f1 | while read line 
do
dpkg-deb --build  $line
done
