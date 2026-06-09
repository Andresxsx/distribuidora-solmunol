#!/bin/sh

php artisan config:clear
php artisan view:clear
php artisan cache:clear

php artisan migrate --force

php artisan db:seed --class=UsuariosRolesSeeder --force || true

php artisan storage:link || true

php artisan config:cache
php artisan view:cache

php artisan serve --host=0.0.0.0 --port=${PORT}
