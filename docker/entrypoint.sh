#!/bin/sh
set -e

cd /var/www/html

# Ensure writable directories exist and have proper permissions
mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar public/uploads
chown -R www-data:www-data writable public/uploads 2>/dev/null || true
chmod -R 775 writable public/uploads 2>/dev/null || true

# Generate .env from Docker environment variables
cat > .env <<ENVEOF
CI_ENVIRONMENT = ${CI_ENVIRONMENT:-production}

app.baseURL = '${APP_URL:-https://e-super.sman1prambon.my.id/}'
app.appTimezone = '${APP_TIMEZONE:-Asia/Jakarta}'

database.default.hostname = ${DB_HOST:-e-supervisi-db}
database.default.database = ${DB_DATABASE:-supervisi_guru}
database.default.username = ${DB_USERNAME:-supervisi}
database.default.password = ${DB_PASSWORD:-SmapraSupervisi2026!}
database.default.DBDriver = ${DB_DRIVER:-MySQLi}
database.default.port = ${DB_PORT:-3306}
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_general_ci

encryption.key = ${ENCRYPTION_KEY:-hex2bin:08f6ba003e41a2013a910a4928c40ee6}
ENVEOF

chown www-data:www-data .env
chmod 640 .env

exec "$@"
