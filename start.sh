#!/bin/sh
# Jalankan PHP-FPM di background
php-fpm &

#run web socket client
php -f /usr/src/myapp/wsc/wsc.php &

#php -i

# Jalankan Caddy di foreground
caddy run --config /etc/caddy/Caddyfile --adapter caddyfile