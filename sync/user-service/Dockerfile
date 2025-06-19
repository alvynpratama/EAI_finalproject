# Menggunakan image PHP dengan FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Install ekstensi PHP yang diperlukan (misalnya, MySQL, Redis)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Set working directory
WORKDIR /var/www

# Salin seluruh file proyek Laravel ke dalam kontainer
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependensi Laravel
RUN composer install --optimize-autoloader --no-dev

# Set izin file untuk Laravel
RUN chown -R www-data:www-data /var/www

# Expose port untuk aplikasi
EXPOSE 80

# Jalankan PHP-FPM
CMD ["php-fpm"]
