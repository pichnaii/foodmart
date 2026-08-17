FROM php:8.2-apache

# Copy application files to the web server directory
COPY . /var/www/html/

# Enable Apache mod_rewrite if needed for routing
RUN a2enmod rewrite

# Expose port 80 for Render
EXPOSE 81