#!/bin/sh

php artisan config:clear || true
php artisan view:clear || true
php artisan cache:clear || true

php artisan migrate --force

php artisan db:seed --class=UsuariosRolesSeeder --force || true

php artisan storage:link || true

php artisan filament:assets || true

php artisan config:cache
php artisan view:cache

php artisan serve --host=0.0.0.0 --port=${PORT}