.PHONY: *

php-container:
	docker build --force-rm --no-cache --tag ordinary-uid-php .
