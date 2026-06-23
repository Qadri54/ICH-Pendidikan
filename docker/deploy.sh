#!/bin/bash
set -e

echo "=== ICH-Pendidikan Deployment ==="
cd "$(dirname "$0")/.."

# Build image baru
echo "[1/6] Building image..."
docker compose build --no-cache

# Jalankan semua container (force-recreate agar env terbaru dimuat)
echo "[2/6] Starting containers..."
docker compose up -d --force-recreate

# Tunggu MySQL ready
echo "[3/6] Waiting for MySQL..."
docker compose exec app php -r "
  \$tries = 0;
  while (\$tries < 30) {
    try {
      new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
      echo \"MySQL connected!\n\";
      exit(0);
    } catch (Exception \$e) {
      \$tries++;
      echo \"Attempt \$tries/30...\n\";
      sleep(2);
    }
  }
  echo \"MySQL tidak bisa terkoneksi setelah 30 percobaan.\n\";
  exit(1);
"

# Migrasi database
echo "[4/6] Running migrations..."
docker compose exec app php artisan migrate --force

# Storage link
echo "[5/6] Linking storage..."
docker compose exec app php artisan storage:link 2>/dev/null || true

# Optimasi Laravel
echo "[6/6] Optimizing Laravel..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache

# Restart nginx untuk refresh DNS upstream ke container baru
docker compose restart nginx

echo ""
echo "=== Deployment selesai! ==="
echo "Akses: http://$(hostname -I | awk '{print $1}')"
