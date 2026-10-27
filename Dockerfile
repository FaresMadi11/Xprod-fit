FROM php:8.2-apache

# 1. Habilitar mod_rewrite para el enrutamiento limpio del MVC y .htaccess
RUN a2enmod rewrite

# 2. Instalar dependencias del sistema y extensiones de PHP (PDO MySQL, GD para imágenes)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3. Configurar Apache para que escuche en el puerto 10000 requerido por Render
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# 4. Permitir que .htaccess controle las rutas en DocumentRoot
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 5. Copiar el código del proyecto a /var/www/html
WORKDIR /var/www/html
COPY . /var/www/html/

# 6. Permisos de carpetas para subidas y almacenamiento
RUN chown -R www-data:www-data /var/www/html/uploads /var/www/html/storage 2>/dev/null || true \
    && chmod -R 775 /var/www/html/uploads /var/www/html/storage 2>/dev/null || true

# 7. Render mapea el puerto 10000 por defecto
EXPOSE 10000

# 8. Script de inicio que vincula dinámicamente la variable $PORT de Render
CMD sh -c "sed -i \"s/Listen 10000/Listen \${PORT:-10000}/g\" /etc/apache2/ports.conf && sed -i \"s/*:10000/*:\${PORT:-10000}/g\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"
