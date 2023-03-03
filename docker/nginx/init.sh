#!/bin/bash
apt  update --quiet
apt  install -y --quiet cron nano wget
# Configurando cron
service cron start
crontab -l | echo '*/59 * * * * /bin/sh /var/www/storage/app/script/gera-logs.sh' | crontab -
# Configurando localtime
rm -rf /etc/localtime
ln -s /usr/share/zoneinfo/America/Campo_Grande /etc/localtime
dpkg-reconfigure -f noninteractive tzdata
