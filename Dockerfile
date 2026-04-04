FROM php:8.4-apache-trixie

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json /var/www/html
COPY composer.lock /var/www/html

RUN composer install --no-interaction --optimize-autoloader --no-dev

COPY ./src/www ./
COPY ./src/includes/views ../includes/views
COPY ./src/lib ./lib
COPY ./vendor ./vendor
# COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN chown -R www-data:www-data /var/www/
RUN chmod -R 755 /var/www/html

RUN composer dump-autoload --optimize

# Create  a dot env file with variables to match your setup. These are for example.
#ENV DB_HOST='localhost'
#ENV DB_USER='bandwidth'
#ENV DB_PASSWORD='secret'
#ENV DB_NAME='router'




EXPOSE 80
