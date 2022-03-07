#!/bin/bash
set -euo pipefail

APP_ENV=$1
PHP_VER=$2

# -----------------------------------------------------------------------------
# PHP & PHP-FPM
# -----------------------------------------------------------------------------

# Make env vars available. See https://github.com/docker-library/php/blob/1ad18817e5de82df1a8855fe711de0b3c0c320b9/7.4/buster/fpm/Dockerfile
sed -i \
    -e "s/^;clear_env.*/clear_env = no/g" \
    /etc/php/${PHP_VER}/fpm/pool.d/www.conf

sed -i \
    -e "s/^expose_php.*/expose_php = Off/g" \
    -e "s/^memory_limit.*/memory_limit = 256M/g" \
    -e "s/^post_max_size.*/post_max_size = 150M/g" \
    -e "s/^upload_max_filesize.*/upload_max_filesize = 150M/g" \
    -e "s/^;date\.timezone.*/date\.timezone = America\/New_York/g" \
    -e "s/^;\(include_path = \".:\/usr\/share\/php*\)/\1/g" \
    /etc/php/${PHP_VER}/fpm/php.ini

sed -i \
    -e "s/^error_log.*/error_log = \/proc\/self\/fd\/2/g" \
    /etc/php/${PHP_VER}/fpm/php-fpm.conf

sed -i \
    -e "s/^max_execution_time.*/max_execution_time = 120/g" \
    -e "s/^memory_limit.*/memory_limit = 512M/g" \
    -e "s/^;date\.timezone.*/date\.timezone = America\/New_York/g" \
    -e "s/^;\(include_path = \".:\/usr\/share\/php*\)/\1/g" \
    -e "s/^;error_log = syslog.*/error_log = \/proc\/self\/fd\/2/g" \
    /etc/php/${PHP_VER}/cli/php.ini

sed -i \
    -e "s/^user =.*/user = www-data/g" \
    -e "s/^group =.*/group = www-data/g" \
    -e "s/^listen.owner =.*/listen.owner = www-data/g" \
    -e "s/^listen.group =.*/listen.group = www-data/g" \
    -e "s/^pm.max_children =.*/pm.max_children = 16/g" \
    -e "s/^pm.max_requests =.*/pm.max_requests = 1000/g" \
    -e "s/^;access.log =.*/access.log = \/proc\/self\/fd\/2/g" \
    -e "s/^;catch_workers_output =.*/catch_workers_output = yes/g" \
    -e "s/^;decorate_workers_output =.*/decorate_workers_output = no/g" \
    /etc/php/${PHP_VER}/fpm/pool.d/www.conf

# -----------------------------------------------------------------------------
# WEB SERVER
# -----------------------------------------------------------------------------

# Setup apache configs and mods
cp /opt/wp-project-skeleton/docker/conf/apache/sites-available/default.conf /etc/apache2/sites-available/default.conf
cp /opt/wp-project-skeleton/docker/conf/apache/conf-available/* /etc/apache2/conf-available/

# Setup apache configs and mods
{ \
    echo 'ServerName localhost'; \
} > /etc/apache2/conf-available/localhost.conf

a2dissite 000-default && \
    a2dismod mpm_prefork && \
    a2ensite default && \
    a2enmod mpm_event alias deflate expires ext_filter filter headers mime proxy proxy_fcgi rewrite setenvif && \
    a2enconf localhost logs security php${PHP_VER}-fpm

# -----------------------------------------------------------------------------
# CLI APPLICATIONS
# -----------------------------------------------------------------------------

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# WP CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

# -----------------------------------------------------------------------------
# STAGING & PRODUCTION SETTINGS
# -----------------------------------------------------------------------------

if [[ "${APP_ENV}" == "staging" || "${APP_ENV}" == "production" ]]; then

    # Opcache - See https://www.php.net/manual/en/opcache.installation.php
    { \
        echo 'opcache.revalidate_freq=60'; \
    } >> /etc/php/${PHP_VER}/mods-available/opcache.ini

    # Error logging
    { \
		echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
		echo 'display_errors = Off'; \
		echo 'display_startup_errors = Off'; \
		echo 'log_errors = On'; \
		echo 'error_log = /dev/stderr'; \
		echo 'log_errors_max_len = 1024'; \
		echo 'ignore_repeated_errors = On'; \
		echo 'ignore_repeated_source = Off'; \
		echo 'html_errors = Off'; \
	} > /etc/php/${PHP_VER}/mods-available/error-logging.ini

else

# -----------------------------------------------------------------------------
# DEVELOPMENT SETTINGS
# -----------------------------------------------------------------------------

    # msmtp with mailhog
    apt-get update && apt-get install -y --no-install-recommends msmtp

    { \
        echo "account default"; \
        echo "host mailhog"; \
        echo "port 1025"; \
        echo "auto_from on"; \
    } > /etc/msmtprc

    # Error logging
    { \
        echo 'error_reporting = E_ALL'; \
        echo 'display_errors = On'; \
        echo 'display_startup_errors = On'; \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
        echo 'log_errors_max_len = 1024'; \
        echo 'ignore_repeated_errors = On'; \
        echo 'ignore_repeated_source = Off'; \
        echo 'html_errors = On'; \
    } > /etc/php/${PHP_VER}/mods-available/error-logging.ini

    # Don't push access logs to stdout on dev
    sed -i \
        -e "s/^.*TransferLog.*/CustomLog \$\{APACHE_LOG_DIR\}\/access.log combined/g" \
        /etc/apache2/sites-available/default.conf

fi

phpenmod error-logging
