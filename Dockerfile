FROM php:8.2-apache

COPY ./src/www /var/www/html/
COPY ./src/includes /var/www/includes/
COPY ./src/lib /var/www/lib/
COPY ./src/vendor /var/www/vendor/
# COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN chown -R www-data:www-data /var/www/
RUN chmod -R 755 /var/www/html

ENV DB_HOST='localhost'
ENV DB_USER='bandwidth'
ENV DB_PASSWORD='secret'
ENV DB_NAME='router'

RUN docker-php-ext-install mysqli pdo pdo_mysql

EXPOSE 80
