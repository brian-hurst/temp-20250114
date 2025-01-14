FROM php:8.3-fpm

RUN apt-get update \
    && apt -y upgrade \
    && apt-get install -y --no-install-recommends \
    vim  \
    libcurl4-openssl-dev  \
    git  \
    zlib1g-dev  \
    libzip-dev  \
    libfreetype6-dev  \
    libjpeg62-turbo-dev  \
    libpng-dev  \
    unzip  \
    apt-utils  \
    wget  \
    libxml2-dev  \
    acl  \
    mariadb-client \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl soap bcmath opcache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install symfony-cli

RUN apt -y autoremove
# Docker dev modifications.
RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini
RUN echo 'max_execution_time = 60' >> /usr/local/etc/php/conf.d/docker-php-max_execution.ini

RUN rm /etc/nginx/sites-enabled/*
COPY ./docker/php-fpm-nginx/nginx.conf /etc/nginx/sites-enabled/
COPY ./docker/php-fpm-nginx/www.conf /usr/local/etc/php-fpm.d/www.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=600

###> recipes ###
###< recipes ###

WORKDIR /var/www/html

COPY ./docker/php-fpm-nginx/script.sh /etc/script.sh
ENTRYPOINT ["/etc/script.sh"]