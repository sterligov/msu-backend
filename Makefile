.PHONY: fixtures
fixtures:
	docker exec msu-php php bin/console hautelook:fixtures:load --purge-with-truncate -n

unit-tests:
	docker exec msu-php ./vendor/bin/simple-phpunit --group=unit

functional-tests: fixtures
	docker exec msu-php ./vendor/bin/simple-phpunit --group=functional

all-tests: fixtures
	docker exec msu-php ./vendor/bin/simple-phpunit


