#!/usr/bin/env bash

RED='\033[0;31m' # RED
GREEN='\033[0;32m' # GREEN
NC='\033[0m' # No Color

if [ -d "/var/www/html" ]
then
    php "`dirname \"$0\"`"/phpstan-config-generator.php
    composer dump-autoload

    RESULT=$(php ../../../dev-ops/analyze/vendor/bin/phpstan analyze --configuration phpstan.neon --autoload-file=../../../vendor/autoload.php src|grep "ERROR")
    
    if [ -n "$RESULT" ]
    then
        echo "${RED}ERROR: Static code analysis FAILED.${NC}"
        read -r -p "Want to see the full report? [y/N]" response

        case "$response" in
            [yY][eE][sS]|[yY]) 
                php ../../../dev-ops/analyze/vendor/bin/phpstan analyze --configuration phpstan.neon --autoload-file=../../../vendor/autoload.php src
                ;;
        esac
        echo "\n"
        exit 1
    else
        echo "${GREEN}Static code analysis PASSED.${NC}"
    fi
else
    echo "Execute this command inside the docker container."
    exit 1;
fi
