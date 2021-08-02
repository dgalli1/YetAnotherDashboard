FROM composer:latest
COPY . /app
WORKDIR /app
RUN composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
RUN rm composer.json composer.lock

FROM php:8.0-apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/webroot/
COPY --from=0 /app /var/www/html
RUN a2enmod rewrite
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get update
RUN apt-get install -y nodejs gcc g++ make libglu1 libxi6 libgconf-2-4
WORKDIR /var/www/html/resources
RUN npm install
WORKDIR /var/www/html/webroot
RUN mkdir assets res
WORKDIR /var/www/html/
RUN mkdir cache

WORKDIR /var/www/html
RUN chown www-data:www-data -R .
