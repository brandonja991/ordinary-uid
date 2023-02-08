.PHONY: *

php-image:
	docker build --tag ordinary-uid-php .

dependencies:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer install --no-progress

phplint:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phplint

phpcs:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phpcs

psalm:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script psalm

phpunit:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script phpunit

test:
	docker run -it --rm -v .:/opt/project -w /opt/project ordinary-uid-php composer run-script test
