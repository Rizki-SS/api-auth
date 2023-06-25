
FROM composer:2.4.1 as composer

FROM php:alpine3.18 as runtime

FROM spiralscout/roadrunner:2023.1.5 as roadrunner

# install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV COMPOSER_HOME="/tmp/composer"

RUN set -x \
    # install permanent dependencies
    && apk add --no-cache \
        postgresql-libs \
        icu-libs \
    # install build-time dependencies
    && apk add --no-cache --virtual .build-deps \
        postgresql-dev \
        linux-headers \
        autoconf \
        openssl \
        make \
        g++ \
    # install PHP extensions (CFLAGS usage reason - https://bit.ly/3ALS5NU)
    && CFLAGS="$CFLAGS -D_GNU_SOURCE" docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        sockets \
        opcache \
        pcntl \
        intl \
        1>/dev/null \
    && adduser \
        --disabled-password \
        --shell "/sbin/nologin" \
        --home "/nonexistent" \
        --no-create-home \
        --uid "10001" \
        --gecos "" \
        "appuser" \
    # create directory for application sources and roadrunner unix socket
    && mkdir /app /var/run/rr \
    && chown -R appuser:appuser /app /var/run/rr \
    && chmod -R 777 /var/run/rr

COPY --from=roadrunner /usr/bin/rr /usr/bin/rr

USER appuser:appuser

WORKDIR /app

COPY --chown=appuser:appuser ./apps/composer.* /app/

RUN composer install -n --no-dev --no-cache --no-ansi --no-autoloader --no-scripts --prefer-dist

COPY --chown=appuser:appuser ./apps/ /app/

RUN set -x \
    # generate composer autoloader and trigger scripts
    && composer dump-autoload -n --optimize \
    # "fix" composer issue "Cannot create cache directory /tmp/composer/cache/..." for docker-compose usage
    && chmod -R 777 ${COMPOSER_HOME}/cache \
    # create the symbolic links configured for the application
    && php ./artisan storage:link