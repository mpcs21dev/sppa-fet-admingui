FROM php:8.1-fpm-alpine3.21

# Install curl dan bash (jika perlu)
RUN apk add --no-cache curl bash
#RUN apk add --no-cache php81-sockets
#RUN apk add --no-cache php81-pdo_pgsql

RUN /bin/sh -c /usr/local/bin/docker-php-ext-enable sockets pdo_pgsql

# Install Caddy
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/alpine.deb.sh' | sh && \
    apk add --no-cache caddy

# Copy aplikasi
COPY www/ /usr/src/myapp
WORKDIR /usr/src/myapp

# Copy file konfigurasi Caddy (buat file Caddyfile di project Anda)
COPY Caddyfile /etc/caddy/Caddyfile

# Script untuk menjalankan PHP-FPM dan Caddy secara bersamaan
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
