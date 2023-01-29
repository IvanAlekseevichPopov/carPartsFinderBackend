FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    fonts-liberation \
    fontconfig \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    locales \
    locales-all \
    xvfb \
    zip \
    zlib1g-dev

#panther support
#RUN apt-get install -y \
#    chromium \
#    chromium-driver

RUN fc-cache -f -v

RUN docker-php-ext-install \
    bcmath \
    intl \
    opcache \
    pdo \
    pdo_pgsql \
    zip

#Liip Imagine support
#RUN apt-get install -y \
#        jpegoptim \
#        libfreetype6-dev \
#        libjpeg62-turbo-dev \
#        libmcrypt-dev \
#        libpng-dev \
#        optipng \
#    && pecl install mcrypt-1.0.1; docker-php-ext-enable mcrypt \
#    && docker-php-ext-install -j$(nproc) iconv \
#    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
#    && docker-php-ext-install -j$(nproc) gd

#RUN pecl install xdebug; \
#    docker-php-ext-enable xdebug

ENV PROMPT_COMMAND="history -a"
ENV HISTTIMEFORMAT="%h %d %H:%M:%S "
ENV HISTFILESIZE=20000
ENV LC_ALL en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8

#ENV PANTHER_NO_SANDBOX=1
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

#RUN composer global require dbrekelmans/bdi --prefer-dist --no-progress --no-suggest --optimize-autoloader --no-interaction \
#    && php /root/.config/composer/vendor/bin/bdi detect drivers

RUN chown -R www-data:www-data /usr/local/etc/php/; \
    chmod -R 775 /usr/local/etc/php/
