#!/bin/sh

echo "Install submodules"
git submodule init
git submodule update

if [ ! -f ./.env ]; then
	echo "Copy framework .env"
    	cp ./.env.example ./.env
fi

if [ ! -f ./docker/.env ]; then
	echo "Copy docker .env"
    	cp ./docker/env-example ./docker/.env
fi
  
cd docker

docker-compose build nginx postgres
docker-compose up -d nginx postgres

docker-compose exec workspace bash -c 'composer install; ./compile-frontend.sh; php artisan key:generate; php artisan migrate; php artisan vendor:publish --tag=money; php artisan l5-swagger:generate; vendor/bin/phpunit'

