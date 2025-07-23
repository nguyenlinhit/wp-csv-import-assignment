FROM wordpress:php8.3-apache

# Enable some necessary modules
RUN docker-php-ext-install mysqli

# Copy all WP code (available in repo)
COPY ./web /var/www/html

# Write permissions for uploads (disk mounted to mount path, but still set)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (Render reads automatically)