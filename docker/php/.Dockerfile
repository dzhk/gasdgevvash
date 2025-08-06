FROM php:8.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000


RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip


RUN groupadd -g ${GROUP_ID} appgroup \
    && useradd -u ${USER_ID} -g appgroup -m -d /home/appuser -s /bin/bash appuser \
    && usermod -aG www-data appuser
RUN chown -R appuser:appgroup /var/www


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/story_valut

USER appuser