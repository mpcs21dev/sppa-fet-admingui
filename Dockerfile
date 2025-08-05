FROM shinsenter/php:8.4-fpm-nginx

# Install php module
RUN phpaddmod sqlite sockets pdo_sqlite curl

# Install curl dan bash (jika perlu)
RUN apk add --no-cache curl bash sqlite3 fileinfo
#RUN apk add --no-cache php81-sockets
#RUN apk add --no-cache php81-pdo_pgsql

# Copy aplikasi
WORKDIR /var/www
COPY www ./html
COPY wsc ./wsc

#COPY start.sh /start.sh
#RUN chmod +x /start.sh

CMD ["php","-f","/var/www/wsc/wsc.php"]
