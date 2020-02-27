#!/bin/bash
cd /var/www
php artisan migrate
php artisan passport:install
exec "$@"
# php artisan serve --port 8000 --host 0.0.0.0
