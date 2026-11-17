FROM php:8.3-apache
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
COPY src /var/www/html/src
COPY public /var/www/html/public

# Показувати помилки лише в розробці; у продакшені вимкнути
RUN printf "display_errors=On\nerror_reporting=E_ALL\n" > /usr/local/etc/php/conf.d/dev.ini
