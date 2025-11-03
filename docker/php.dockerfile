from php:8.3-fpm-alpine

run apk add libpq libpq-dev \
    && cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
    && apk add zlib zlib-dev libpng libpng-dev freetype freetype-dev libjpeg-turbo libjpeg-turbo-dev  \
    && docker-php-ext-configure gd --with-freetype --with-jpeg  --enable-gd \
    && docker-php-ext-install gd pdo_pgsql \
    && apk del libpq-dev libpng-dev zlib-dev freetype-dev libjpeg-turbo-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
