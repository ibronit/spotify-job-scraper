.PHONY: up vendor build

build: vendor
	docker-compose build

up:
	docker-compose up

vendor: composer.json
	docker run --rm --interactive --tty --volume ${PWD}:/app composer:1.9 install