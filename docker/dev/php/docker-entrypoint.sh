#!/bin/sh

: ${WWW_DATA_UID:=`stat -c %u /var/www/html`}

# Change www-data's uid & guid to be the same as directory in host or the configured one
if [ "`id -u www-data`" != "$WWW_DATA_UID" ]; then
    usermod -u $WWW_DATA_UID www-data
fi

touch /var/www/.bash_history
mkdir -p /var/www/.cache
chown -R www-data: /var/www/.cache /var/www/.bash_history

php-fpm -R
