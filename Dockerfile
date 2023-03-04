FROM composer:2.5.1 AS deps

WORKDIR /app

COPY . .

RUN rm -rf /config/vhosts

RUN composer install

#-----------------------
FROM php:8.2.1-apache AS server-setup

COPY ./config/vhosts /etc/apache2/sites-enabled

RUN echo "ServerName blog" >> /etc/apache2/apache2.conf

WORKDIR /var/www

#-----------------------
FROM php:8.2.1-apache AS runner

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=server-setup /etc/apache2 /etc/apache2

RUN rm /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www

COPY --from=deps /app  .

RUN \
  addgroup --system --gid 1001 apache && \
  adduser --system --uid 1001 apache && chown apache:apache -R .

USER apache