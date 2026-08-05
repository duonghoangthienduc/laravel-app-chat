FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    postgresql-client \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    linux-headers \
    busybox-extras \
    nodejs \
    npm \
    $PHPIZE_DEPS

RUN docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath pcntl exif opcache

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/php/migrate.sh /usr/local/bin/migrate
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/migrate

WORKDIR /var/www