#!/bin/bash
set -euo pipefail

APP_ENV=$1
ENV_PASSPHRASE=$2

# -----------------------------------------------------------------------------
# .ENV SETUP
# -----------------------------------------------------------------------------

if [[ "${APP_ENV}" == "staging" || "${APP_ENV}" == "production" ]]; then

    ENV_ENCRYPTED_FILE=/opt/wp-project-skeleton/docker/env/${APP_ENV}.aes

    if [[ -e ${ENV_ENCRYPTED_FILE} ]]; then
        # --
        # Note that the digest is explicitly set since older openssl versions will
        # use MD5 while newer versions will use SHA256. Currently the latest version
        # of openssl in homebrew is using MD5 by default. To update the .env file and
        # encrypt, use the following command:
        #
        # openssl enc -aes-256-cbc -md SHA256 -in .env -out app_env_val_here.aes -pass pass:***
        # --
        openssl enc -d -aes-256-cbc -md SHA256 -in ${ENV_ENCRYPTED_FILE} -out /opt/wp-project-skeleton/.env -pass pass:${ENV_PASSPHRASE}
    fi

fi
