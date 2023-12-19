$(info Importing ".docker-compose.env" file.)
include .docker-compose.env

ifneq ("$(wildcard ./.docker-compose.env.local)","")
  $(info Importing ".docker-compose.env.local" file.)
  include .docker-compose.env.local
endif

export

start:
	docker/current-user.sh
	docker compose up --force-recreate

stop:
	docker compose stop

shell-php:
	docker compose exec php-fpm bash

rebuild-php:
	docker compose stop php-fpm
	docker compose build --pull php-fpm

logs-php:
	docker compose logs -f php

status:
	docker compose ps

wipe:
	docker compose down -v --remove-orphans
