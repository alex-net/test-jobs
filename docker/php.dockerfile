from php:8.3-fpm-alpine

run apk add libpq libpq-dev \
    && cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
    && docker-php-ext-install pdo_pgsql \
    && apk del libpq-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
