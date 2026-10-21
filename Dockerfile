FROM php:8.5-fpm

# CAMBIO: mysqli reemplaza pdo_mysql para conectar con la base de datos.
# mbstring cuenta caracteres UTF-8.
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install -j$(nproc) mbstring mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
