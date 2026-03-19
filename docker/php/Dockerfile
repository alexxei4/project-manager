FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && docker-php-ext-install pdo_pgsql pgsql


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/backend

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www


COPY --chown=www:www . /var/www/backend


USER www

EXPOSE 9000
CMD ["php-fpm"]
