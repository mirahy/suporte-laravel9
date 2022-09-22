FROM composer:2.2 as builder


FROM php:7.4-apache

WORKDIR /var/www

ENV PHP_EXTRA_CONFIGURE_ARGS: "--with-mysqli --with-pgsql"
ENV APACHE_DOCUMENT_ROOT=/var/www/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 16M!g' $PHP_INI_DIR/php.ini* 
RUN sed -ri -e 's!post_max_size = 8M!post_max_size = 32M!g' $PHP_INI_DIR/php.ini*
RUN sed -i '/<Directory ${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
        git cron nano wget libzip-dev unzip libldap2-dev libpq-dev  \
    && docker-php-ext-configure zip \
    && docker-php-ext-install mysqli pdo pdo_mysql pgsql pdo_pgsql zip ldap

COPY . .

COPY --from=builder /usr/bin/composer /usr/bin/composer

RUN chown www-data:www-data storage -R
RUN chmod +x storage/app/script/*

ADD cron-file /etc/cron.d/my-cron-file
RUN chmod 0644 /etc/cron.d/my-cron-file
RUN crontab /etc/cron.d/my-cron-file

ADD entrypoints/00_cron /opt/run/
ADD entrypoints/01_apache /opt/run/
RUN chmod +x /opt/run/*

ADD entrypoints/run_all /opt/bin/
RUN chmod +x /opt/bin/run_all

RUN useradd -u 1000 user \
    && addgroup user www-data

RUN composer update

ENTRYPOINT ["/opt/bin/run_all"]
