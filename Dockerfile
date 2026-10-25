FROM php:8.5-apache

# Habilitar mod_rewrite para el enrutamiento limpio del sistema MVC
RUN a2enmod rewrite

# Instalar extensiones necesarias de PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar el código del proyecto al DocumentRoot de Apache
COPY . /var/www/html/

# Ajustar configuración de Apache para permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Asignar permisos al servidor web para uploads y storage
RUN chown -R www-data:www-data /var/www/html/uploads /var/www/html/storage 2>/dev/null || true

EXPOSE 80

CMD ["apache2-foreground"]
