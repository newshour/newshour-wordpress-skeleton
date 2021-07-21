# -----------------------------------------------------------------------------
# Dockerfile for running an Ubuntu LAMP stack.
# -----------------------------------------------------------------------------
FROM ubuntu:20.04

# Set extra arg/env vars.
ARG APP_ENV
ARG PHP_VER=7.4
ENV DEBIAN_FRONTEND noninteractive

RUN set -eux; \
	apt-get update; \
	apt-get install -q -y --no-install-recommends --no-install-suggests \
        apache2 \
        apache2-dev \
        apt-transport-https \
        apt-utils \
        bash \
        ca-certificates \
        curl \
        gnupg2 \
        htop \
        libapache2-mod-fcgid \
        openssl \
        php-bcmath \
        php-bz2 \
        php-cli \
        php-curl \
        php-fpm \
        php-imagick \
        php-intl \
        php-gd \
        php-json \
        php-mbstring \
        php-memcached \
        php-mysql \
        php-opcache \
        php-pear \
        php-xmlrpc \
        php-zip \
        vim \
        zip \
        unzip \
        wget \
    ; \
    rm -rf /var/lib/apt/lists/*

# Needed for socket address
RUN mkdir -p /run/php && \
    mkdir -p /opt/wp-project-skeleton

COPY --chown=www-data:www-data . /opt/wp-project-skeleton
WORKDIR /opt/wp-project-skeleton

# Setup apache.
COPY docker/conf/apache/sites-available/default.conf /etc/apache2/sites-available/default.conf
COPY docker/conf/apache/conf-available/* /etc/apache2/conf-available/

# Run scripts
COPY ./docker/scripts /opt/docker/scripts
RUN chmod u+x /opt/docker/scripts/launch.sh && \
    chmod u+x /opt/docker/scripts/setup.sh && \
    /opt/docker/scripts/setup.sh "${APP_ENV}" "${PHP_VER}"

# Cleanup
RUN apt-get purge -q -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 80

# Launch apache + php-fpm
ENTRYPOINT ["/opt/docker/scripts/launch.sh"]
CMD ["7.4"]