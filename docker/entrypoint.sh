#!/bin/sh
set -e

cd /var/www/html

# Run migrations
php artisan migrate --force

# Optional seed when RUN_SEED=1, ignore errors if admin already exists
if [ "${RUN_SEED}" = "1" ]; then
  php artisan db:seed --class=Database\\Seeders\\AdminSeeder --force 2>&1 || true
fi

# Ensure admin user exists and password is correct
php artisan tinker --execute="
\$admin = App\Models\Admin::where('email', 'admin@nominaempleados.com')->first();
if (!\$admin) {
  App\Models\Admin::create([
    'nombre' => 'Administrador Principal',
    'email' => 'admin@nominaempleados.com',
    'password' => bcrypt('admin123'),
  ]);
  echo 'Admin user created.';
} else {
  \$admin->update(['password' => bcrypt('admin123')]);
  echo 'Admin password updated.';
}
" || true

# Start Apache in foreground
exec apache2-foreground
