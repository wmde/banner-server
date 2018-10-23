FROM php:7.2-fpm as app

RUN mkdir -p /usr/share/nginx/www/banner.wikipedia.de/current/var/cache \
    && mkdir -p /usr/share/nginx/www/banner.wikipedia.de/current/var/log \
    && chown -R www-data:www-data /usr/share/nginx/www/banner.wikipedia.de/current/var

RUN apt-get update \
    # for intl
    && apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip

RUN curl https://getcomposer.org/installer | php -- --filename=composer --install-dir=/bin

FROM app as app_debug

RUN pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
