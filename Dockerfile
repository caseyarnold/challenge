FROM php:8.4-apache

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

# COPY ./public /var/www/html
# COPY ./src/ /var/www

EXPOSE 80

CMD ["apache2-foreground"]