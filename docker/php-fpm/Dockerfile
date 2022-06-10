ARG PHP_VERSION
FROM "php:${PHP_VERSION}-fpm"

USER root

RUN (echo "alias ll='ls -alh --color=auto'" && echo ) >> ~/.bashrc

ARG NODE_VERSION
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash -

RUN apt-get update && apt-get install -y \
    curl wget git \
    libpng-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    libonig-dev libzip-dev libmcrypt-dev \
    libzip-dev libwebp-dev libxpm-dev \
    locales zip jpegoptim optipng pngquant gifsicle \
    vim unzip imagemagick libmagickwand-dev \
    default-mysql-client dirmngr apt-transport-https \
    lsb-release ca-certificates zlib1g-dev

RUN apt-get install -y nodejs build-essential

# Install redis
#RUN pecl install -o -f redis \
#&& rm -rf /tmp/pear \
#&& docker-php-ext-enable redis
RUN printf "\n" | curl 'http://pecl.php.net/get/redis-5.3.7.tgz' -o redis-5.3.7.tgz \
    && pecl install redis-5.3.7.tgz \
    &&  rm -rf redis-5.3.7.tgz \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis


# Install PECL extensions
# see http://stackoverflow.com/a/8154466/291573) for usage of `printf`
#RUN pecl install -o -f imagick \
#&& rm -rf /tmp/pear \
#&& docker-php-ext-enable imagick

# Install extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring zip exif pcntl iconv bcmath intl

RUN set -ex \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y libmemcached-dev libicu-dev memcached \
    && rm -rf /var/lib/apt/lists/* \
    && MEMCACHED="`mktemp -d`" \
    && curl -skL https://github.com/php-memcached-dev/php-memcached/archive/master.tar.gz | tar zxf - --strip-components 1 -C $MEMCACHED \
    && docker-php-ext-configure $MEMCACHED \
    && docker-php-ext-install $MEMCACHED \
    && rm -rf $MEMCACHED

#INFO: PHP7.4 check or newer -- --with-png not needed, older versions - needed

ARG PHP_WITH_GD
RUN if [ ${PHP_WITH_GD} = true ]; then \
        docker-php-ext-configure gd --with-webp --with-jpeg \
        --with-xpm --with-freetype \
        && docker-php-ext-install gd \
;fi

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN usermod -p "*" www-data -s /bin/bash
RUN usermod -u 1000 www-data

WORKDIR /usr/local/docker-entrypoint

RUN chown www-data:www-data /usr/local/docker-entrypoint
COPY ./init.sh init.sh
RUN chown www-data:www-data init.sh
RUN chmod +x init.sh

ARG APP_PATH
WORKDIR /var/www
#COPY ${APP_PATH} .
RUN chown -R www-data:www-data ./

USER www-data

RUN (echo "alias ll='ls -alh --color=auto'" && echo ) >> ~/.bashrc

# Запускаем контейнер
# Из документации: The main purpose of a CMD is to provide defaults for an executing container. These defaults can include an executable,
# or they can omit the executable, in which case you must specify an ENTRYPOINT instruction as well.
EXPOSE 9000
CMD ["php-fpm"]
