FROM php:7.2-fpm as app

RUN mkdir -p /usr/share/nginx/www/banner.wikipedia.de/current/var/cache \
    && mkdir -p /usr/share/nginx/www/banner.wikipedia.de/current/var/log \
    && chown -R www-data:www-data /usr/share/nginx/www/banner.wikipedia.de/current/var

RUN apt-get update \
    # for intl
    && apt-get install -y libicu-dev \
    # for curl
    && apt-get install -y libcurl3-dev
