# Utiliser une image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions PHP courantes
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql zip

# Activer mod_rewrite pour Apache (utile pour les URLs propres)
RUN a2enmod rewrite

# Copier le code source dans le conteneur
COPY . /var/www/html/

# Copier la configuration Apache si elle existe
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
