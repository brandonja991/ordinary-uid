.PHONY: *

php-container:
	docker build --force-rm --no-cache --tag ordinary-uid-php .

phpunit:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phpunit
