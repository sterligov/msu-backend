.PHONY: fixtures

docker-up:
	docker-compose -f docker-compose.yml up -d --build

docker-test-up:
	docker-compose -f docker-compose.test.yml up -d --build

deploy:
	chmod +x deploy.sh
	./deploy.sh

test-env:
	chmod +x test.sh
	./test.sh

production-env:
	chmod +x prod.sh
	./prod.sh

development-env:
	chmod +x dev.sh
	./dev.sh

fixtures:
	docker exec msu-php php bin/console hautelook:fixtures:load --purge-with-truncate -n

unit-tests:
	docker exec msu-php ./vendor/bin/simple-phpunit --group=unit

functional-tests: fixtures
	docker exec msu-php ./vendor/bin/simple-phpunit --group=functional

all-tests: fixtures
	docker exec msu-php ./vendor/bin/simple-phpunit


