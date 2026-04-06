#!/usr/bin/env bash

RED='\033[0;31m' # RED
GREEN='\033[0;32m' # GREEN
NC='\033[0m' # No Color

if [ -d "/var/www/html" ]
then
    if [ -f "./vendor/bin/phpinsights" ]
    then
        RESULT=$(php ./vendor/bin/phpinsights analyse src \-s \--min-quality=90 \--min-complexity=70 \--min-architecture=90 \--min-style=90|grep "ERROR")
    else
        composer require nunomaduro/phpinsights --dev
        
        RESULT=$(php ./vendor/bin/phpinsights analyse src \-s \--min-quality=90 \--min-complexity=70 \--min-architecture=90 \--min-style=90|grep "ERROR")
    fi
    
    if [ -n "$RESULT" ]
    then
        echo "${RED}ERROR: Code quality is not good enough.${NC}"
        echo "${GREEN}PASS = Code >90%; Complexity >70%; Architecture >90%; Style >90%${NC}"
        read -r -p "Want to see the full report? [y/N]" response

        case "$response" in
            [yY][eE][sS]|[yY]) 
                php ./vendor/bin/phpinsights analyse src --min-quality=90 --min-complexity=70 --min-architecture=90 --min-style=90
                ;;
        esac
        echo "\n"
        exit 1
    else
        echo "${GREEN}Code quality analysis PASSED.${NC}"
        echo "\n"
    fi
else
    echo "Execute this command inside the docker container."
    exit 1;
fi
