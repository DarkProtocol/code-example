FROM spiralscout/roadrunner:2.11.2 AS roadrunner

FROM php:8.1.6-cli-alpine3.14 AS base

RUN apk update --no-cache \
    && apk add --no-cache \
        autoconf \
        bash \
        composer \
        dcron \
        g++ \
        git \
        gmp-dev \
        make \
        nano \
        openssh-client \
        postgresql-dev \
        libzip-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        freetype-dev \
#        postgresql-client \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gmp \
        opcache \
        pdo \
        pdo_pgsql \
        pgsql \
        pcntl \
        sockets \
        gd \
    && pecl install -o -f \
        redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable \
        redis \
    && mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini \
# Since RoadRunner workers run from CLI, OPCache needs to be enabled for CLI as well
# See: https://roadrunner.dev/docs/app-server-production/2.x/en#configuration
    && echo opcache.enable_cli=1 > $PHP_INI_DIR/conf.d/opcache-cli.ini

WORKDIR /app

COPY composer.json composer.lock ./

ENV COMPOSER_MEMORY_LIMIT -1

# Flag --ignore-platform-reqs for iexbase/tron-api lib
RUN composer install --no-dev --no-autoloader --ignore-platform-reqs

# Artisan GUI frontend build is required because there is no recent changes in pre-built version.
# TODO: remove this when upstream (inFureal/artisan-gui) uploads new build
RUN apk add --no-cache --virtual .node \
        nodejs \
        npm \
    && cd ./vendor/infureal/artisan-gui \
    && npm install \
    && npx mix \
    && cd /app \
    && apk del .node

COPY . .
COPY --from=roadrunner /usr/bin/rr /app/rr

RUN composer dump-autoload --optimize --classmap-authoritative \
    && chmod 777 /app/docker/crontab \
    && crontab /app/docker/crontab \
    && chown -R www-data:www-data /app/storage \
    && chmod -R 777 /app/storage \
    && find /app/docker/ -type f -iname "*.sh" -print0 | xargs -0 chmod +x \
    && chmod +x rr

# Make binaries from composer packages
# available without path prefixes
ENV PATH ./vendor/bin:$PATH

ENTRYPOINT ["/app/docker/entrypoint.sh"]

CMD ["php", "artisan", "octane:start", "--host=0.0.0.0"]

FROM base AS test

# Flag --ignore-platform-reqs for iexbase/tron-api lib
RUN composer install --ignore-platform-reqs

CMD ["phpunit"]

FROM base AS dev

RUN apk add --no-cache \
        nodejs \
        npm \
    && npm install

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && composer install
