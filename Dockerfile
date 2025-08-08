FROM php:8.1-fpm-alpine3.21
 
# Install curl dan bash
RUN apk add --no-cache curl bash libxml2-dev postgresql-dev sqlite-dev libsodium-dev && \
    docker-php-ext-install pdo pdo_sqlite pdo_pgsql sodium sockets
 
# Install Caddy
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/alpine.deb.sh' | sh && \
    apk add --no-cache caddy
 
# Copy aplikasi
COPY www/ /usr/src/myapp
COPY wsc/ /usr/src/myapp/wsc
WORKDIR /usr/src/myapp
 
# Copy file konfigurasi Caddy
COPY Caddyfile /etc/caddy/Caddyfile
 
# Salin dan beri izin eksekusi file start.sh
COPY start.sh /start.sh
RUN chmod +x /start.sh
 
# Jalankan start.sh untuk memulai PHP-FPM dan Caddy
CMD ["/start.sh"]
