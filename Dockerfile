FROM php:8.3.2-cli-alpine3.18 as base

# Install Composer globally
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/

# Set non-interactive to avoid service management errors
RUN set -ex && \
    apk update && \
    apk add --no-cache \
        libstdc++ \
        libpq \
        libpng-dev \
        libjpeg-turbo-dev \
        python3 \
        py3-pip \
        python3-dev \
        gcc \
        g++ \
        make \
        wget \
        nodejs \
        npm \
        bash \
        git && \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        curl-dev \
        linux-headers \
        openssl-dev \
        pcre-dev \
        pcre2-dev \
        zlib-dev && \
    pecl channel-update pecl.php.net && \
    pecl install --configureoptions 'enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no"' redis-6.0.2 && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    docker-php-ext-enable redis && \
    docker-php-ext-install sockets bcmath pcntl exif && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-enable gd && \
    mkdir -p /usr/src/php/ext/openswoole && \
    curl -sfL https://github.com/openswoole/ext-openswoole/archive/v22.1.2.tar.gz -o openswoole.tar.gz && \
    tar xfz openswoole.tar.gz --strip-components=1 -C /usr/src/php/ext/openswoole && \
    docker-php-ext-configure openswoole --enable-openssl --enable-sockets --enable-hook-curl && \
    docker-php-ext-install -j$(nproc) --ini-name zzz-docker-php-ext-openswoole.ini openswoole && \
    rm -f openswoole.tar.gz $HOME/.composer/*-old.phar && \
    docker-php-source delete

# Install Python 3.11 explicitly and set as default
RUN apk add --no-cache python3=3.11.0-r0 && \
    python3 -m ensurepip --upgrade && \
    python3 -m pip install --upgrade pip

# Create and activate Python virtual environment using Python 3.11
RUN python3 -m venv /opt/venv && \
    /opt/venv/bin/pip install --no-cache-dir numpy==1.24.3 && \
    /opt/venv/bin/pip install --no-cache-dir Pillow==10.0.0 && \
    /opt/venv/bin/pip install --no-cache-dir onnxruntime==1.15.1 && \
    /opt/venv/bin/pip install --no-cache-dir rembg==2.0.50 && \
    mkdir -p /home/swoole/.u2net && \
    wget https://github.com/danielgatis/rembg/releases/download/v0.0.0/u2net_lite.onnx -O /home/swoole/.u2net/u2net_lite.onnx

# Create swoole user
RUN adduser -D -h /home/swoole -s /bin/bash swoole && \
    chown -R swoole:swoole /home/swoole/.u2net

WORKDIR /home/swoole

# Copy composer files first
COPY --chown=swoole:swoole composer.json composer.lock ./

# Copy .env file
COPY --chown=swoole:swoole .env.example ./.env

# Copy the rest of the project files
COPY --chown=swoole:swoole . .

USER swoole

# Install dependencies and setup
RUN composer install --no-scripts && \
    composer dump-autoload && \
    mkdir -p /home/swoole/database && \
    touch /home/swoole/database/database.sqlite && \
    chmod -R 775 /home/swoole/database && \
    php artisan key:generate --force && \
    php artisan migrate --force && \
    php artisan storage:link && \
    php artisan cache:clear && \
    composer clear-cache && \
    npm ci

EXPOSE 8080

CMD ["php", "artisan", "octane:start", "--watch", "--host=0.0.0.0", "--port=8080"]
