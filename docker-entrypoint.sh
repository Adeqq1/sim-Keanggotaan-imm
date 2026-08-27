#!/bin/sh
set -eu

if [ "$(id -u)" = 0 ]; then
    mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache
    chown -R appuser:appuser storage bootstrap/cache
    chmod -R u+rwX storage bootstrap/cache
fi

exec docker-php-entrypoint "$@"
