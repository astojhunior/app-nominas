#!/bin/sh
set -e

cd /var/www/html

# Run migrations
php artisan migrate --force

# Optional seed when RUN_SEED=1
if [ "${RUN_SEED}" = "1" ]; then
  php artisan db:seed --class=Database\\Seeders\\AdminSeeder --force
fi

# Start Apache in foreground
exec apache2-foreground
