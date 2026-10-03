#!/bin/sh
set -e

# Generate APP_KEY if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

php artisan package:discover --ansi

# Ensure database directory and sqlite file exist with write permissions
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod -R 777 /var/www/html/database
chmod -R 777 /var/www/html/storage

# Run database migrations and seeders (including 120 Lucknow electricians)
php artisan migrate --force
php artisan db:seed --force

# Clear cached config to pick up runtime environment variables
php artisan config:clear
php artisan route:clear
php artisan view:clear

PORT="${PORT:-8080}"
echo "Starting ElectroLKO server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
