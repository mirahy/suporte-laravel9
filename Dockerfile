FROM php:8.2-fpm

ENV PHP_EXTRA_CONFIGURE_ARGS: "--with-mysqli --with-pgsql --with-zip"

RUN sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 16M!g' $PHP_INI_DIR/php.ini* 
RUN sed -ri -e 's!post_max_size = 8M!post_max_size = 32M!g' $PHP_INI_DIR/php.ini* 

RUN apt-get update && apt-get install -y \
        git cron nano wget libzip-dev unzip libldap2-dev libpq-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install mysqli pdo pdo_mysql pgsql pdo_pgsql zip ldap

RUN chown -R www-data:www-data /var/www
COPY . /var/www
WORKDIR /var/www
RUN chmod -R 777 storage
RUN chown www-data:www-data storage -R
RUN useradd -u 1000 user \
    && addgroup user www-data
RUN rm -rf /etc/localtime \
    && ln -s /usr/share/zoneinfo/America/Campo_Grande /etc/localtime \
    &&  dpkg-reconfigure -f noninteractive tzdata
RUN service cron start \
    && echo "*/2 * * * * /bin/sh /var/www/storage/app/script/gera-logs.sh">crontab -e

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# RUN git pull
# RUN composer update







