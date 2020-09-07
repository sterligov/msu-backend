#!/bin/sh

ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no ${USER}@${HOST} "\
    set -e && \
    cd ${PROJECT_PATH} && \
    git pull && \
    docker-compose -f docker-compose.yml up -d --build && \
    docker exec -t msu-php composer install --no-dev"
