#!/bin/sh

/usr/bin/wget -O /var/www/storage/app/logs-exportacao-estudantes/out-`date "+%a"`'.log' http://localhost/estudantes-grupo-lotes
