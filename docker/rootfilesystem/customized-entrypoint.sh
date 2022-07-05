#!/usr/bin/env bash

set -e

echo "##### This line is printed out from customized-entrypoint.sh ####"

# Set php.ini default config
cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/conf.d/php.ini
# cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# No vendor directory, no dependencies. Install them.
[ ! -d "/var/www/vendor" ] && composer install || echo "vendor directory already exists"
[[ ! $(composer validate --no-check-publish) =~ "is not valid" ]] && composer update || echo "composer.json and composer.lock are synced"
[ ! -f "/var/www/.env" ] && cp .env.local .env || echo ".env already exists"

[ ! -d "/var/www/logs" ] && mkdir logs

. /entrypoint.sh
