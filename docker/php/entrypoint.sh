#!/bin/sh
set -e

if [ "$DB_CONNECTION" = "pgsql" ]; then
  echo "⏳ Waiting for PostgreSQL at $DB_HOST:$DB_PORT..."
  until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" > /dev/null 2>&1; do
    sleep 1
  done
  echo "✅ PostgreSQL is ready!"
fi

if [ -f artisan ]; then
  chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true
fi

if [ -f artisan ] && [ "$RUN_MIGRATIONS" = "true" ]; then
  echo "🚀 Running database migrations..."
  php artisan migrate
  echo "✅ Migrations completed."
fi

if [ -f package.json ] && [ "$BUILD_ASSETS" = "true" ]; then
  echo "📦 Installing npm dependencies and building frontend assets..."
  npm ci
  npm install
  npm run build:all
  echo "✅ Frontend assets built."
fi

exec "$@"