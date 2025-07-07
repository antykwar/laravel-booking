FROM php:8.4-fpm-alpine

# Установка постоянных зависимостей
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    libssh-dev \
    openssl-dev \
    hiredis-dev \
    linux-headers \
    rabbitmq-c-dev \
    supervisor \
    git \
    unzip

# Установка временных зависимостей для сборки
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS

# Установка расширений PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring exif pcntl bcmath soap sockets

# Установка Redis и AMQP расширений
RUN pecl install amqp redis \
    && docker-php-ext-enable amqp redis

# Очистка временных зависимостей
RUN apk del .build-deps

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка Node.js и npm
RUN apk add --no-cache nodejs npm

WORKDIR /var/www/html

# Оптимизация для production (для разработки можно удалить)
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Рекомендуется для разработки (удалить для production)
RUN composer global require laravel/installer \
    && ln -s /root/.composer/vendor/bin/laravel /usr/local/bin/laravel
