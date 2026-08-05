#!/bin/sh
set -e

# ============================================
# Manual Migration Script
# ============================================

if [ ! -f artisan ]; then
  echo "❌ artisan not found. Are you in the Laravel root directory?"
  exit 1
fi

echo "⏳ Waiting for database..."
if [ "$DB_CONNECTION" = "pgsql" ]; then
  until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" > /dev/null 2>&1; do
    sleep 1
  done
fi
echo "✅ Database is ready!"

echo "🚀 Running migrations..."
if [ "$APP_ENV" = "production" ]; then
  php artisan migrate --force
else
  php artisan migrate
fi
echo "✅ Done."