FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
  bash \
  git \
  su-exec

RUN set -ex; \
  apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libjpeg-turbo-dev \
    libwebp-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
  ; \
  \
  docker-php-ext-configure intl; \
  docker-php-ext-install -j "$(nproc)" \
    bcmath \
    ctype \
    exif \
    fileinfo \
    mysqli \
    pdo \
    pdo_mysql \
    opcache \
    zip \
    pcntl \
    intl \
  ; \
  \
  runDeps="$( \
    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
      | tr ',' '\n' \
      | sort -u \
      | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
  )"; \
  apk add --virtual .laravel-phpexts-rundeps $runDeps; \
  apk del .build-deps


COPY ./docker/laravel/laravel.json /docker-entrypoint.d/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

VOLUME /var/www/html
WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 9000

RUN set -ex; \
  chown -R www-data:www-data /var/www/html

COPY ./docker/laravel/app.ini /usr/local/etc/php/conf.d/app.ini
