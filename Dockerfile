
FROM laravelsail/php84-composer:latest AS vendor
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-gd

FROM php:8.4-apache
WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libpng-dev \
        libzip-dev \
        libicu-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql bcmath intl gd zip opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf '\n<Directory /var/www/html/storage/app/public>\n    Options FollowSymLinks\n    AllowOverride None\n    Require all granted\n</Directory>\n' >> /etc/apache2/sites-available/000-default.conf

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/ecolacup-entrypoint

RUN sed -i 's/\r$//' /usr/local/bin/ecolacup-entrypoint \
    && chmod +x /usr/local/bin/ecolacup-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["/usr/local/bin/ecolacup-entrypoint"]
CMD ["apache2-foreground"]
