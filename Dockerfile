FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd intl pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# The source folder is bind-mounted from Fedora and owned by UID/GID 1000.
# Run Apache workers as that user so Laravel can write cache, logs, sessions, and uploads.
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
    && sed -ri 's/^: \$\{APACHE_RUN_USER:=.*/: ${APACHE_RUN_USER:=#1000}/; s/^: \$\{APACHE_RUN_GROUP:=.*/: ${APACHE_RUN_GROUP:=#1000}/' \
        /etc/apache2/envvars
