#!/usr/bin/env bash

set -e

echo "##### This line is printed out from customized-entrypoint.sh ####"

# Set php.ini default config
cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/conf.d/php.ini
# cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

. /entrypoint.sh
