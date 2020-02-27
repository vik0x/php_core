FROM php:7.4-fpm

# Set working directory
WORKDIR /var/www/back

# Copy composer.lock and composer.json
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.lock composer.json ./

# Install dependencies
RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    # git \
    curl

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql
    
# Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www/back
RUN composer install
ENV PATH="~/.composer/vendor/bin:./vendor/bin:${PATH}"

# Change current user to www
RUN chown -R www-data:www-data /var/www

USER root

# Expose port 9000 and start php-fpm server
COPY ./install.sh /temp/install.sh
RUN chmod +x /temp/install.sh
EXPOSE 7000
CMD ["php-fpm"]
ENTRYPOINT [ "/temp/install.sh" ]
