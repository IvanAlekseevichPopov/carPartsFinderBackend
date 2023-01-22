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

#ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN usermod -s /bin/bash www-data; \
    touch /var/www/.bash_history; \
    chmod 777 /var/www/.bash_history

RUN chown -R www-data:www-data /usr/local/etc/php/; \
    chmod -R 775 /usr/local/etc/php/

ENV PROMPT_COMMAND="history -a"
ENV HISTTIMEFORMAT="%h %d %H:%M:%S "
ENV HISTFILESIZE=20000
ENV LC_ALL en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8

#COPY ./symfony.ini /usr/local/etc/php/conf.d
#COPY ./symfony.pool.conf /usr/local/etc/php-fpm.d/

   14  Jan 21 15:22:25 apt-get install chromium-chromedriver firefox-geckodriver
   15  Jan 21 15:22:31 apt-get update
   16  Jan 21 15:22:34 apt-get install chromium-chromedriver firefox-geckodriver
   17  Jan 21 15:24:58 wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
   18  Jan 21 15:25:03 apt install wget
   19  Jan 21 15:25:06 wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
   20  Jan 21 15:25:17 apt install gnupg
   21  Jan 21 15:25:23 wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
   22  Jan 21 15:25:35 echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list
   23  Jan 21 15:25:40 apt-get update -y
   24  Jan 21 15:25:48 apt-get install -y google-chrome-stable
   25  Jan 21 15:29:15 wget -q https://dl.google.com/linux/direct/google-chrome-stable_current_arm8.deb
   26  Jan 21 15:29:18 ls
   27  Jan 21 15:29:22 wget -q https://dl.google.com/linux/direct/google-chrome-stable_current_arm.deb
   28  Jan 21 15:30:21 apt-get install chromium-browser
   29  Jan 21 15:30:30 apt-get install chromium*
   30  Jan 21 15:30:46 apt-get install chromium-browser
   31  Jan 21 15:30:49 apt-get install chromium-browser*
   32  Jan 21 15:31:03 apt-get install chromium-driver
