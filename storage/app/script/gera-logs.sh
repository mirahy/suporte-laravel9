#!/bin/sh

# /usr/bin/wget -O /var/www/storage/app/logs-exportacao-estudantes/out-`date "+%a"`'.log' http://host-laravel-2/estudantes-grupo-lotes

/usr/bin/wget -O /var/www/storage/app/logs-exportacao-estudantes/out-`date +"%a-%T"`'.log' http://web-service-2/estudantes-grupo-lotes
