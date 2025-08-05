FROM shinsenter/php:8.4-fpm-nginx

# Install php module
# RUN phpaddmod sqlite sockets pdo_sqlite curl


# Copy aplikasi
WORKDIR /var/www
COPY www ./html
COPY wsc ./wsc

#COPY start.sh /start.sh
#RUN chmod +x /start.sh

CMD ["php","-f","/var/www/wsc/wsc.php"]
