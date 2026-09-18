FROM antrian-spmb:latest

USER root

# Install intl extension required by CodeIgniter 4
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    && docker-php-ext-install -j$(nproc) intl \
    && rm -rf /var/lib/apt/lists/*

# Apache DocumentRoot points to CodeIgniter 4 public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN echo '<Directory /var/www/html/public/>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/ci4.conf \
    && a2enconf ci4

# Production PHP settings
RUN { \
    echo 'upload_max_filesize = 20M'; \
    echo 'post_max_size = 25M'; \
    echo 'memory_limit = 256M'; \
    echo 'date.timezone = Asia/Jakarta'; \
    echo 'opcache.enable = 1'; \
    echo 'opcache.memory_consumption = 128'; \
    echo 'opcache.interned_strings_buffer = 16'; \
    echo 'opcache.max_accelerated_files = 10000'; \
    echo 'opcache.validate_timestamps = 1'; \
    echo 'opcache.revalidate_freq = 2'; \
} > /usr/local/etc/php/conf.d/production.ini

WORKDIR /var/www/html

# Copy application files (with vendor)
COPY . /var/www/html

# Set directory permissions
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 writable

EXPOSE 80

CMD ["apache2-foreground"]
