#!/bin/sh
set -e

echo "[entrypoint] Waiting for PostgreSQL..."
until pg_isready -h database -p 5432 -U "${POSTGRES_USER:-symfony}" -q; do
  sleep 1
done
echo "[entrypoint] PostgreSQL is ready."

echo "[entrypoint] Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

if [ ! -f config/jwt/private.pem ]; then
  echo "[entrypoint] Generating JWT keypair..."
  php bin/console lexik:jwt:generate-keypair
else
  echo "[entrypoint] JWT keypair already exists, skipping."
fi

echo "[entrypoint] Starting PHP-FPM..."
exec docker-php-entrypoint php-fpm
