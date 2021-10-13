#!/bin/bash
set -euo pipefail

# Start PHP-FPM if we are starting Apache.
if [ "$1" = "/usr/sbin/apache2ctl" ]; then
    /usr/sbin/php-fpm7.4 -D
fi

exec "$@"
