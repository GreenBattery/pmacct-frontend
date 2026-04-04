FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www

RUN pwd
RUN ls -al

COPY ./composer.json ./
COPY ./composer.lock ./

RUN composer install --no-interaction --optimize-autoloader --no-dev



COPY ./src/lib ./lib
# this is relative to the context of the docker-compose
COPY ./src/batch/stats-day-calc/stats.day.calc.php ./


RUN composer dump-autoload --optimize

RUN cat vendor/autoload.php

CMD ["php", "stats.day.calc.php", "--current"]
