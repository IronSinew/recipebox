#!/bin/sh

cd /var/www/app && composer install --no-plugins --no-scripts

cat /tmp/websockets.conf > /etc/supervisor/supervisord.conf

service cron start

supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf