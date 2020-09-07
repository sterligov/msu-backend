#!/bin/sh

docker-compose -f docker-compose.test.yml up -d --build
docker exec msu-php composer install
docker exec msu-php bin/console doctrine:migrations:migrate -n
docker exec msu-php bin/console messenger:setup-transports failed
docker exec msu-php bin/console messenger:setup-transports async

docker exec -u root msu-php bash -c 'apk add openssl && \
    apk add acl && \
    mkdir -p config/jwt && \
    jwt_passphrase=${JWT_PASSPHRASE:-$(grep "^JWT_PASSPHRASE=" .env | cut -f 2 -d "=")} && \
    echo $jwt_passphrase && \
    echo $jwt_passphrase | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 && \
    echo $jwt_passphrase | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout && \
    chown -R 1000:1000 config/jwt'

docker exec msu-php bin/console hautelook:fixtures:load --purge-with-truncate -n


