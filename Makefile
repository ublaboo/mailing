.PHONY: phpstan tests

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

tests:
	vendor/bin/tester -s -p php --colors 1 -C tests
