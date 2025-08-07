#!/bin/sh

set -e

cd /var/www/html

supervisord -c /etc/supervisord.conf &

echo "Composer install..."
composer install

wait
