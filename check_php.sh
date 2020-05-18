#!/bin/bash

php -version | grep PHP |grep --only-matching --perl-regexp "\d+\.\\d+\.\\d+" | cut -d"." -f1
