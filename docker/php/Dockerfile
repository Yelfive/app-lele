FROM php:7-fpm
MAINTAINER Felix Huang <yelfivehuang@gmail.com>

# install extensions of php
RUN apt-get update && apt-get install -y \
        apt-utils \
        libfreetype6-dev \
        libxslt1-dev \
        libzip-dev \
        libedit-dev \
        libxml2-dev \
        libtidy-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libicu-dev \
        libxml2-dev \
        libenchant-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) bcmath calendar dba enchant exif \
    && docker-php-ext-install -j$(nproc) ftp gettext \
    && docker-php-ext-install intl mcrypt mysqli \
    && docker-php-ext-install opcache pcntl pdo_mysql \
    && docker-php-ext-install shmop soap sockets sysvmsg sysvsem sysvshm tidy wddx xmlrpc \
    && docker-php-ext-install -j$(nproc) xsl zip \

# Mac need to set www-data's uid 1000 and the group of staff
# RUN usermod -u 1000 www-data && usermod -G staff www-data
    
