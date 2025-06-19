# Menggunakan image PHP versi terbaru dengan FPM
FROM php:8.2-fpm

# Install ekstensi yang diperlukan
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

# Install Composer untuk dependency management
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set directory kerja di dalam container
WORKDIR /var/www

# Salin semua file project Laravel ke dalam container
COPY . .

# Install dependency Laravel menggunakan Composer
RUN composer install

# Ubah permission file agar dapat diakses oleh user www-data
RUN chown -R www-data:www-data /var/www

# Expose port yang digunakan oleh Laravel
EXPOSE 8000

# Jalankan server Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]