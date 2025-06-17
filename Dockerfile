FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# ...existing code...
COPY . /var/www
WORKDIR /var/www

# ตั้งค่า DocumentRoot ไปที่ public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN a2enmod rewrite
