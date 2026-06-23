#!/bin/bash
set -e

echo "=== ICH-Pendidikan Deployment ==="

# Build & start containers
docker compose build --no-cache
docker compose up -d

# Wait for MySQL to be ready
echo "Menunggu MySQL ready..."
docker compose exec app php -r "
  \$tries = 0;
  while (\$tries < 30) {
    try {
      new PDO('mysql:host=mysql;dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
      echo \"MySQL connected!\\n\";
      exit(0);
    } catch (Exception \$e) {
      \$tries++;
      echo \"Attempt \$tries/30...\\n\";
      sleep(2);
    }
  }
  echo \"MySQL tidak bisa terkoneksi setelah 30 percobaan.\\n\";
  exit(1);
"

# Run Laravel setup commands
echo "Running migrations..."
docker compose exec app php artisan migrate --force

echo "Optimizing Laravel..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache

echo "Linking storage..."
docker compose exec app php artisan storage:link

echo "=== Deployment selesai! ==="
echo "Akses aplikasi di: http://$(hostname -I | awk '{print $1}')"
