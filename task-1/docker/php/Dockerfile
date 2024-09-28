FROM php:8.3-cli

# Устанавливаем необходимые пакеты для компиляции расширений
RUN apt-get update && apt-get install -y \
    libicu-dev \
    g++ \
    git \
    libzip-dev \
    && docker-php-ext-install intl bcmath zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app