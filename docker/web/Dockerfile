# Apache with PHP 8.2 to try the example pages in examples/ (see SETUP.md)
FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# show errors in the browser, like a development server
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
