#!/usr/bin/env bash

CURRENT_DIR=$(pwd)
APP_DIR=$CURRENT_DIR|grep -o 'html'

if [ -d "/var/www/html" ]
then
    php ./vendor/bin/phpinsights analyse src
else
    echo "Execute this command inside the docker container."
    exit 1;
fi
