from php:8.3-fpm-alpine

run cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
    && docker-php-ext-install pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
