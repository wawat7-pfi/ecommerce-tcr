#!/bin/bash
set -e

# Automatically fix permissions for plugin storage directory on container startup
if [ -d "/var/www/html/wp-content/plugins/all-in-one-wp-migration" ]; then
    mkdir -p /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage 2>/dev/null || true
    chown -R www-data:www-data /var/www/html/wp-content/plugins/all-in-one-wp-migration 2>/dev/null || true
    chmod -R 775 /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage 2>/dev/null || true
fi

# Execute original entrypoint command (apache2-foreground)
exec "$@"
