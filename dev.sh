#!/bin/sh

docker-compose -f docker-compose.yml up -d --build
docker exec msu-php composer install
docker exec msu-php bin/console doctrine:migrations:migrate -n
docker exec msu-php yarn install

docker exec -u root msu-php bash -c 'apk add openssl && \
    apk add acl && \
    mkdir -p config/jwt && \
    jwt_passphrase=${JWT_PASSPHRASE:-$(grep "^JWT_PASSPHRASE=" .env | cut -f 2 -d "=")} && \
    echo $jwt_passphrase && \
    echo $jwt_passphrase | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 && \
    echo $jwt_passphrase | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout && \
    chown -R 1000:1000 config/jwt'

docker exec msu-php bin/console hautelook:fixtures:load --purge-with-truncate -n

media_folder=public/media/$(date +%Y)/$(date +%m)
mkdir -p "$media_folder"
cp -r fixtures/media/* "$media_folder"

docker exec msu-php bin/console messenger:setup-transports failed
docker exec -u root msu-php bash -c 'supervisord -c /etc/supervisord.conf'
#docker exec -u root -it msu-php bash -c 'supervisorctl reread && supervisorctl update && supervisorctl start messenger-consume:*'
#docker exec -u root -it msu-php supervisorctl restart messenger-consume:messenger-consume_00
