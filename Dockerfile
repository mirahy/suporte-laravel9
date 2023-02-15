FROM php:8.1-fpm

ENV PHP_EXTRA_CONFIGURE_ARGS: "--with-mysqli --with-pgsql --with-zip"

RUN sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 16M!g' $PHP_INI_DIR/php.ini* 
RUN sed -ri -e 's!post_max_size = 8M!post_max_size = 32M!g' $PHP_INI_DIR/php.ini*

RUN apt-get update && apt-get install -y \
        git cron nano wget libzip-dev unzip libldap2-dev libpq-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install mysqli pdo pdo_mysql pgsql pdo_pgsql zip ldap

WORKDIR /var/www
COPY . .
RUN chown www-data:www-data storage -R
RUN chmod -R 777 storage/.
RUN chmod +x storage/app/script/*
RUN useradd -u 1000 user \
    && addgroup user www-data

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# RUN composer update

