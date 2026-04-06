#!/usr/bin/env bash

VERSION=$(grep -Po '"version":\s?"(?<version>.*)",' ./composer.json|grep -Po '(\d|\.)+' $VERSION)
RED='\033[0;31m'
GREEN='\033[0;32m'
RESET='\033[0m'

if printf -- '%s' "$(cat ./CHANGELOG.md)" | egrep -q -- "$VERSION"; then
     echo -e "${GREEN}Verified CHANGELOG.md contains note for current version...${RESET}"
else
    echo -e "${RED}CHANGELOG.md is missing change notes for current version! Exiting...${RESET}"
    exit 1;
fi

if printf -- '%s' "$(cat ./CHANGELOG_de-DE.md)" | egrep -q -- "$VERSION"; then
     echo -e "${GREEN}Verified CHANGELOG_de-DE.md contains note for current version...${RESET}"
else
    echo -e "${RED}CHANGELOG.md is missing change notes for current version! Exiting...${RESET}"
    exit 1;
fi

if printf -- '%s' "$(sh ./bin/check-todos.sh)" | egrep -q -- "OK"; then
    echo -e "${GREEN}No TODOs found...${RESET}"
else
    echo -e "${RED}Complete TODO's before building.${RESET}"
    sh ./bin/check-todos.sh
    exit 1;
fi

sh ./bin/static-analyze.sh || exit 1
sh ./bin/quality-analyze.sh || exit 1

PLUGIN_NAME=${PWD##*/}

echo -e "${GREEN}Building $PLUGIN_NAME distribution $VERSION...${RESET}"

mkdir ./$PLUGIN_NAME

cp -r src ./$PLUGIN_NAME/src
cp -r CHANGELOG.md ./$PLUGIN_NAME/CHANGELOG.MD
cp -r CHANGELOG_de-DE.md ./$PLUGIN_NAME/CHANGELOG_de-DE.md
cp -r composer.json ./$PLUGIN_NAME/composer.json
cp -r LICENSE ./$PLUGIN_NAME/LICENSE
cp -r README.md ./$PLUGIN_NAME/README.md

zip -r $PLUGIN_NAME-$VERSION.zip $PLUGIN_NAME
rm -rf ./$PLUGIN_NAME

mv ./$PLUGIN_NAME-$VERSION.zip ./dist/

echo -e "${GREEN}Done! New distribution is available at ./dist/$PLUGIN_NAME-$VERSION.zip${RESET}"
