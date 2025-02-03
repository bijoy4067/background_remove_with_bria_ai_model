FROM php:8.3.2-cli-alpine3.18 as base

COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/

# Install PHP and Python dependencies
RUN set -ex && \
    apk update && \
    apk add --no-cache \
        libstdc++ \
        libpq \
        libpng-dev \
        libjpeg-turbo-dev \
        libgomp \
        libffi-dev \
        zlib-dev \
        jpeg-dev \
        freetype-dev \
        libxml2-dev \
        libzip-dev \
        oniguruma-dev \
        git \
        unzip && \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        curl-dev \
        linux-headers \
        openssl-dev \
        pcre-dev \
        pcre2-dev \
        zlib-dev \
        autoconf \
        g++ \
        make \
        cmake \
        build-base && \
    pecl channel-update pecl.php.net && \
    pecl install --configureoptions 'enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no"' redis-6.0.2 && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    docker-php-ext-enable redis && \
    docker-php-ext-install sockets && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install pcntl && \
    docker-php-ext-install exif && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-enable gd && \
    # install openswoole
    mkdir -p /usr/src/php/ext/openswoole && \
    curl -sfL https://github.com/openswoole/ext-openswoole/archive/v22.1.2.tar.gz -o openswoole.tar.gz && \
    tar xfz openswoole.tar.gz --strip-components=1 -C /usr/src/php/ext/openswoole && \docker-php-ext-configure openswoole \
        --enable-openssl \
        --enable-sockets --enable-hook-curl && \
    docker-php-ext-install -j$(nproc) --ini-name zzz-docker-php-ext-openswoole.ini openswoole && \
    rm -f openswoole.tar.gz $HOME/.composer/*-old.phar && \
    docker-php-source delete && \
    apk del .build-deps

# Configure PHP
# Nothing to do here, currently.

# Create new user
RUN adduser -D -h /home/swoole -s /bin/bash swoole

# EXPOSE 80
# HEALTHCHECK --interval=15s --timeout=10s --retries=2 CMD curl -f http://localhost:3000/pool/ || exit 1

# Set working directory
WORKDIR /home/swoole

# Copy the entire project
COPY --chown=swoole:swoole ./ ./

# Development image
FROM base as development

# Install npm and the project dependencies
RUN apk add --no-cache npm
RUN npm i

# Run the development server as the swoole user
USER swoole

# Install composer dependencies
COPY --chown=swoole:swoole composer.json ./
COPY --chown=swoole:swoole composer.lock ./

RUN composer install
RUN composer dump-autoload

RUN composer clear-cache
RUN php artisan cache:clear

CMD [ "php", "artisan", "octane:start",  "--watch",  "--host=0.0.0.0", "--port=80" ]

# Production image
FROM base as production

# Run the production server as the swoole user
USER swoole

# Install composer dependencies
COPY --chown=swoole:swoole composer.json ./
COPY --chown=swoole:swoole composer.lock ./

RUN composer install --no-dev
RUN composer dump-autoload

RUN composer clear-cache
RUN php artisan cache:clear

CMD [ "php", "artisan", "octane:start", "--host=0.0.0.0", "--port=80" ]