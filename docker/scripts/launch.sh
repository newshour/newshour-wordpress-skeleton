#!/bin/bash
set -euo pipefail

ln -sf /dev/stdout /var/log/apache2/access.log && ln -sf /dev/stdout /var/log/apache2/error.log

# Start PHP-FPM if we are starting Apache.
if [ "$1" = "/usr/sbin/apache2ctl" ]; then
    /usr/sbin/php-fpm8.1 -D
fi

exec "$@"
