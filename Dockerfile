FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install sqlite extension for database
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Copy project files
COPY . /var/www/html/

# Permissions for uploads and database directory
RUN chown -R www-data:www-data /var/www/html/uploads /var/www/html/database \
    && chmod -R 775 /var/www/html/uploads /var/www/html/database

EXPOSE 80
CMD ["apache2-foreground"]
