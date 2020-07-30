#!/bin/sh

DIR=$(dirname $0)
ROBO_BIN=$DIR/vendor/bin/robo
COMPOSER=$DIR/composer.json
if [ ! -f $ROBO_BIN ] || [ ! -f $COMPOSER ]; then
  echo "MISSING DEPENDENCIES INSTALL COMPOSER AND ALL DEPENDENCIES"
fi

# pass command to Robo
$ROBO_BIN "$@"
