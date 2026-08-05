#!/bin/sh
set -e

# ============================================
# Laravel Docker Entrypoint — Development
# ============================================

# Chờ PostgreSQL sẵn sàng
if [ "$DB_CONNECTION" = "pgsql" ]; then
  echo "⏳ Waiting for PostgreSQL at $DB_HOST:$DB_PORT..."
  until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" > /dev/null 2>&1; do
    sleep 1
  done
  echo "✅ PostgreSQL is ready!"
fi

# Đảm bảo storage & bootstrap/cache ghi được (cần thiết khi bind-mount từ host)
if [ -f artisan ]; then
  chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true
fi

# Chỉ migrate nếu artisan tồn tại (tránh crash-loop lần đầu chưa cài Laravel)
if [ -f artisan ] && [ "$RUN_MIGRATIONS" = "true" ]; then
  echo "🚀 Running database migrations..."
  php artisan migrate
  echo "✅ Migrations completed."
fi

# Build frontend assets nếu chưa có (chỉ chạy trên service được đánh dấu BUILD_ASSETS)
if [ -f package.json ] && [ "$BUILD_ASSETS" = "true" ] && [ ! -f public/build/manifest.json ]; then
  echo "📦 Installing npm dependencies and building frontend assets..."
  npm ci
  npm run build
  echo "✅ Frontend assets built."
fi

exec "$@"