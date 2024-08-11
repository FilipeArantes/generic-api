FROM php:8.2-apache

# Instalação de pacotes e extensões do PHP
RUN apt-get update && apt-get install -y \
    redis-server libxml2-dev autoconf automake libpq-dev libonig-dev libzip-dev libcurl4-openssl-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-install dom pdo pdo_pgsql mbstring curl gd soap zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Instalação e configuração do Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.var_display_max_children=128" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Instalação do Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Configuração do PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configuração do Apache
COPY virtualhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
