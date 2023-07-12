FROM php:8.1.17-fpm-alpine3.17 AS php

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        jq \
        supervisor \
        ffmpeg \
    ;

ARG APCU_VERSION=5.1.22
ARG XDEBUG_VERSION=3.2
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
	    $PHPIZE_DEPS \
	    curl-dev \
	    icu-dev \
	    libzip-dev \
	    zlib-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        freetype-dev \
        libxslt-dev \
	; \
	\
	docker-php-ext-configure zip; \
    docker-php-ext-configure gd  \
        --enable-gd  \
        --with-freetype  \
        --with-jpeg  \
        --with-webp\
    ; \
	docker-php-ext-install -j$(nproc) \
	    intl \
	    zip \
	    pdo_mysql \
        gd \
        exif \
        bcmath \
        xsl \
	; \
	pecl install \
	    apcu-${APCU_VERSION} \
	    xdebug-${XDEBUG_VERSION} \
	    pcov \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
	    apcu \
	    opcache \
	    pcov \
	; \
	\
	runDeps="$( \
	    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
	        | tr ',' '\n' \
	        | sort -u \
	        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
    apk add optipng jpegoptim; \
	\
	apk del .build-deps

# Composer
COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer
#
# Configuration
#
# PHP
ADD .docker/api/php/php.ini /usr/local/etc/php/
ADD .docker/api/php/php-cli.ini /usr/local/etc/php/
# supervisor
#ADD conf/supervisor/messenger-video-upload.ini /etc/supervisor.d/messenger-video-upload.ini
# php-fpm config
ADD .docker/api/php/www.conf /usr/local/etc/php-fpm.d/www.conf

FROM php as local
# Nginx
RUN apk add --no-cache nginx
RUN apk add --no-cache graphviz
ADD .docker/api/nginx/app.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www
COPY .docker/api/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]
EXPOSE 80
RUN echo 'alias sydebug="php -dxdebug.mode=debug -dxdebug.discover_client_host=true -dxdebug.client_port=9000 -dxdebug.client_host=$XDEBUG_CLIENT_HOST bin/console"' >>/etc/profile.d/aliases.sh
RUN echo 'alias ccdebug="php -dxdebug.mode=debug -dxdebug.discover_client_host=true -dxdebug.client_port=9000 -dxdebug.client_host=$XDEBUG_CLIENT_HOST vendor/bin/codecept"' >>/etc/profile.d/aliases.sh

##
# Final build
##
FROM local as deployed

ARG VERSION

COPY --chown=1000:1000 api /var/www
COPY --chown=1000:1000 api/.env /var/www/.env

RUN  mkdir -p /var/www/var/log \
    && mkdir -p /var/www/var/cache \
    && chmod 755 /var/www/var/log \
    && chmod 755 /var/www/var/cache \
    && chown -R 1000:1000 /var/www/var/ \
    && composer install --no-interaction --no-ansi --no-progress --no-suggest --optimize-autoloader \
    && composer dump-autoload -o \
    && rm -rf /var/www/var/cache/* \
&& rm -rf /var/www/html

RUN echo $VERSION > /var/www/VERSION


