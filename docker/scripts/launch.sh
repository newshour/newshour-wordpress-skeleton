#!/bin/bash
set -euo pipefail

PHP_VER=$1

/usr/sbin/php-fpm${PHP_VER} -D && /usr/sbin/apache2ctl -DFOREGROUND

exec "$@"
