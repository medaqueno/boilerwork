#!/usr/bin/env bash

set -e

echo "##### This line is printed out from customized-entrypoint.sh ####"

# Set php.ini default config
cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/conf.d/php.ini
# cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# No vendor directory, no dependencies. Install them.
[ ! -d "/var/www/vendor" ] && composer install || echo "Composer: vendor directory already exists"

composer validate --no-check-publish
[[ $? -eq  0 ]] && echo "Composer: composer.json and composer.lock are synced" || composer update

[ ! -f "/var/www/.env" ] && cp .env.local .env || echo "Env file: .env already exists"

[ ! -d "/var/www/logs" ] && mkdir logs

. /entrypoint.sh
