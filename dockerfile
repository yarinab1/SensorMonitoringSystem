# Use the official PHP image
FROM php:8.1-cli

# Set the working directory
WORKDIR /var/www

# Update and install necessary packages, bypassing GPG errors
RUN apt-get update && \
    apt-get install -y --allow-unauthenticated \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mbstring zip pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the current directory contents into the container
COPY . /var/www

# Install PHP dependencies
RUN composer install

# Expose port 3030
EXPOSE 3030

# Run the PHP built-in server
CMD ["php", "-S", "0.0.0.0:3030", "-t", "public"]
