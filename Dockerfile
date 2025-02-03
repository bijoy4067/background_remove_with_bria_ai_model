FROM php:8.3.2-cli-alpine3.18 as base

COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/

# Install PHP extensions and dependencies
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
    make && \
    apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    curl-dev \
    linux-headers \
    openssl-dev \
    pcre-dev \
    pcre2-dev \
    zlib-dev && \
    # PHP Extensions
    docker-php-ext-install sockets bcmath pcntl exif && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install gd && \
    # Clean up
    apk del .build-deps

# Create new user
RUN adduser -D -h /home/swoole -s /bin/bash swoole

# Set working directory
WORKDIR /home/swoole

# Copy the entire project
COPY --chown=swoole:swoole ./ ./

# Setup Python virtual environment and install dependencies
RUN python3 -m venv venv && \
    source venv/bin/activate && \
    pip3 install --no-cache-dir onnxruntime-cpu && \
    pip3 install --no-cache-dir "rembg[cpu]" && \
    mkdir -p ~/.u2net && \
    wget https://github.com/danielgatis/rembg/releases/download/v0.0.0/u2net_lite.onnx -O ~/.u2net/u2net_lite.onnx

# Development image
FROM base as development

# Install npm and project dependencies
RUN apk add --no-cache npm
RUN npm i

USER swoole

# Install composer dependencies
COPY --chown=swoole:swoole composer.json ./
COPY --chown=swoole:swoole composer.lock ./

RUN composer install && \
    composer dump-autoload && \
    composer clear-cache && \
    php artisan cache:clear

CMD [ "php", "artisan", "octane:start", "--watch", "--host=0.0.0.0", "--port=80" ]

# Production image
FROM base as production

USER swoole

# Install composer dependencies
COPY --chown=swoole:swoole composer.json ./
COPY --chown=swoole:swoole composer.lock ./

RUN composer install --no-dev && \
    composer dump-autoload && \
    composer clear-cache && \
    php artisan cache:clear

CMD [ "php", "artisan", "octane:start", "--host=0.0.0.0", "--port=80" ]