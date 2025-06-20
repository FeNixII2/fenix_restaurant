# FROM php:8.2-apache

# RUN docker-php-ext-install pdo pdo_mysql

# # ...existing code...
# COPY . /var/www
# WORKDIR /var/www

# # ตั้งค่า DocumentRoot ไปที่ public
# RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|g' /etc/apache2/sites-available/000-default.conf
# RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# RUN a2enmod rewrite

FROM php:8.2-apache

# ติดตั้ง cert tools สำหรับ SSL
RUN apt-get update && apt-get install -y \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# คัดลอกไฟล์ทั้งหมด
COPY . /var/www
WORKDIR /var/www

# ตั้งค่า DocumentRoot ให้ชี้ไปที่ /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# คัดลอกไฟล์ SSL cert
COPY ./certs/ca.pem /usr/local/share/ca-certificates/ca.pem

# เพิ่ม cert เข้า trusted CA list
RUN update-ca-certificates

# เปิดใช้ mod_rewrite
RUN a2enmod rewrite

