#!/bin/sh
# Jalankan PHP-FPM di background
php-fpm &

#run web socket client
php -f /usr/src/myapp/wsc/wsc.php &

#php -i

sleep 5

chmod 777 /dev/shm/sppa_fet_log.db

# Jalankan Caddy di foreground
caddy run --config /etc/caddy/Caddyfile --adapter caddyfile