FROM php:8.2-apache

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Instalar dependencias del sistema necesarias para extensiones zip y gd
RUN apt-get update \
    && apt-get install -y \
       libzip-dev \
       libpng-dev \
       libjpeg-dev \
       libfreetype6-dev \
       git \
       unzip \
       curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Setear workdir
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Configurar DocumentRoot para /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar dependencias de PHP sin dev y optimizar autoloader
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan storage:link || true

# Construir assets si aplica
RUN if [ -f package.json ]; then \
      curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
      && apt-get install -y nodejs \
      && npm ci --omit=dev \
      && npm run build; \
    fi

# Permisos para storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Variables típicas para producción
ENV APP_ENV=production \
    APP_DEBUG=false
