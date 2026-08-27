FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd intl mbstring pdo_mysql xml zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# The source folder is bind-mounted from Fedora and owned by UID/GID 1000.
# Run Apache workers as that user so Laravel can write cache, logs, sessions, and uploads.
RUN groupadd --gid 1000 appuser \
    && useradd --uid 1000 --gid 1000 --no-create-home appuser \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
    && sed -ri 's/^: \$\{APACHE_RUN_USER:=.*/: ${APACHE_RUN_USER:=appuser}/; s/^: \$\{APACHE_RUN_GROUP:=.*/: ${APACHE_RUN_GROUP:=appuser}/' \
        /etc/apache2/envvars

RUN printf 'upload_max_filesize=12M\npost_max_size=13M\nmemory_limit=256M\n' > /usr/local/etc/php/conf.d/production.ini

COPY --chmod=755 docker-entrypoint.sh /usr/local/bin/app-entrypoint

ENTRYPOINT ["app-entrypoint"]
