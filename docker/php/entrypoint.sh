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
  if [ ! -f vendor/autoload.php ]; then
    echo "📦 Installing PHP dependencies..."
    composer install --no-interaction
  fi

  chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true
  if [ ! -L public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
    echo "✅ Storage symlink created."
  else
    echo "✅ Storage symlink already exists — skipping."
  fi
fi

if [ -f artisan ] && [ "$RUN_MIGRATIONS" = "true" ]; then
  echo "🚀 Running database migrations..."
  php artisan migrate
  echo "✅ Migrations completed."
fi

if [ -f package.json ] && [ "$BUILD_ASSETS" = "true" ]; then
  if [ ! -x node_modules/.bin/vite ]; then
    echo "📦 Installing npm dependencies..."
    npm ci
  fi

  if [ ! -f public/build/manifest.json ] || [ ! -f public/build-log/manifest.json ]; then
    echo "🏗️ Building frontend and module assets..."
    npm run build:all
    echo "✅ Frontend assets built."
  else
    echo "✅ Frontend manifests already exist — skipping asset build."
  fi
fi

exec "$@"