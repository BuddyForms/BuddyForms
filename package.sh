#!/bin/bash
wd=$PWD
pluginfolder=$wd
originalfoldername=$(basename "$pluginfolder"| awk -F' ' '{print $1}')
packagename=$originalfoldername

r=$(( RANDOM % 10 ));
foldername="$originalfoldername-$r"

echo "Create tmp folder"
echo "/tmp/$foldername"

cp -a "$pluginfolder" /tmp/"$foldername"

cd /tmp/$foldername || exit

version=$(grep "^Stable tag:" $pluginfolder/readme.txt | awk -F' ' '{print $NF}')

echo "Version"
echo $version

echo "-Generating the zip in progress..."

echo "-Cleaning in Progress..."
rm -rf ./.git*
rm -rf ./.sass-cache
rm -rf ./.directory
rm -rf ./node_modules
rm -rf ./wp-config-test.php
rm -rf ./*.yml
rm -rf ./*.xml
rm -rf ./*.dist
rm -rf ./*.neon
rm -rf ./.*.cache
rm -rf ./psalm.xml
rm -rf ./package.json
rm -rf ./package-lock.json
rm -rf ./composer.json
rm -rf ./Gruntfile.js
rm -rf ./gulpfile.js
rm -rf ./composer.lock
rm -rf ./.netbeans*
rm -rf ./.php_cs
rm -rf ./*.zip
rm -rf ./readme.md
#This contain the test stuff
rm -rf ./vendor
rm -rf ./tests
rm -rf ./bin
#Scripts
rm -rf ./*.sh

if [ -f composer.json ]; then
    #Detect if there are composer dependencies
    dep=$(cat composer.json | python -c "import json,sys;sys.stdout.write('true') if 'require' in json.load(sys.stdin)==False else sys.stdout.write('')")
    if [ ! -z ${dep// } ]; then
        echo "-Downloading clean composer dependencies..."
        composer update --no-dev &> /dev/null
    else
        rm -rf composer.json
    fi
fi

echo "Zip to"

echo "$wd"/"$packagename"-"$version".zip

zip -r "$wd"/"$packagename"-"$version".zip ./ &> /dev/null

rm -rf /tmp/"$foldername"

echo "-Done!"
