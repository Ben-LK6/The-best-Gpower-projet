FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite

COPY . /var/www/html/
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
