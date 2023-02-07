.PHONY: *

php-container:
	docker build --force-rm --no-cache --tag ordinary-uid-php .

phplint:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phplint

phpcs:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phpcs

psalm:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script psalm

phpunit:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phpunit

test: phplint phpcs psalm phpunit