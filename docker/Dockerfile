FROM php:8.2-apache

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN docker-php-ext-install mysqli

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

RUN apt update && apt install -yqq nodejs npm

COPY . /var/www/

COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/

RUN composer install
    # npm install --force && \
    # npm run build

ENTRYPOINT [ "bash", "./docker/docker.sh" ]

EXPOSE 80