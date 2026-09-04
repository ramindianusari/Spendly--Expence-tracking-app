# syntax=docker/dockerfile:1
# ============================================================
# PHP 8.2 + Apache — Expense Tracker App Container
# ============================================================

FROM php:8.2-apache

# Enable mod_rewrite for clean URLs (future use)
RUN a2enmod rewrite

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Set the document root to /var/www/html (default)
WORKDIR /var/www/html

# Copy application source into the image
COPY . .

# Allow .htaccess overrides in the web root
RUN sed -i 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

EXPOSE 80
