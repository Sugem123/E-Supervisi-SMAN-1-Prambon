# Stage 1: Build PHP Vendor Dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# Stage 2: Runtime Image
FROM php:8.3-apache

# Install dependencies and required PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Apache DocumentRoot points to CodeIgniter 4 public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN echo '<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
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

# Copy application files
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor

# Permissions
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 writable

EXPOSE 80

CMD ["apache2-foreground"]
