#!/bin/bash
set -e

# Otomatis pastikan folder wp-content, ai1wm-backups, & plugin storage writable oleh www-data
if [ -d "/var/www/html/wp-content" ]; then
    mkdir -p /var/www/html/wp-content/ai1wm-backups 2>/dev/null || true
    mkdir -p /var/www/html/wp-content/uploads 2>/dev/null || true
    mkdir -p /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage 2>/dev/null || true

    chown -R www-data:www-data /var/www/html/wp-content/ai1wm-backups 2>/dev/null || true
    chown -R www-data:www-data /var/www/html/wp-content/plugins/all-in-one-wp-migration 2>/dev/null || true
    chown www-data:www-data /var/www/html/wp-content 2>/dev/null || true

    chmod -R 775 /var/www/html/wp-content/ai1wm-backups 2>/dev/null || true
    chmod -R 775 /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage 2>/dev/null || true
fi

# Execute original entrypoint command (apache2-foreground)
exec "$@"
