#!/bin/sh

#cd /var/www/app && composer install --no-plugins --no-scripts

php /var/www/app/artisan horizon:install

cat /tmp/horizon.conf > /etc/supervisor/supervisord.conf

service cron start

supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf