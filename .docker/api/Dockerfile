FROM php:8.2.24-fpm-alpine3.19 AS php

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        jq \
        nginx \
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
# php-fpm config
ADD .docker/api/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# nginx
ADD .docker/api/nginx/app.conf /etc/nginx/http.d/default.conf

RUN set -eux; \
	apk add --no-cache \
        autoconf \
        build-base \
    ;
WORKDIR /var/www
CMD ["sh"]


FROM php as local

ARG XDEBUG_VERSION=3.2.1
RUN apk add --update linux-headers; \
	pecl install \
	    xdebug-${XDEBUG_VERSION} \
    ;

COPY .docker/api/docker-entrypoint.sh /docker-entrypoint.sh
RUN mkdir -p /home/app/
ENTRYPOINT ["/docker-entrypoint.sh"]
EXPOSE 80

##
# Final build
##
FROM php as final

ARG VERSION

COPY .docker/api/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]
EXPOSE 80

COPY api /var/www
RUN  mkdir -p /var/www/var/log \
    && mkdir -p /var/www/var/cache \
    && chmod 777 /var/www/var/log \
    && chmod 777 /var/www/var/cache \
    && composer install --no-interaction --no-ansi --no-progress --optimize-autoloader \
    && composer dump-autoload -o \
    && rm -rf /var/www/var/cache/*

RUN echo $VERSION > /var/www/VERSION


