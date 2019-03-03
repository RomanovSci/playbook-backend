#!/bin/sh

git submodule init
git submodule update

if [ ! -f ./.env ]; then
    cp ./.env.example ./.env
fi

if [ ! -f ./docker/.env ]; then
    cp ./docker/env-example ./docker/.env
fi

cd docker

docker-compose build nginx postgres redis laravel-horizon
docker-compose up -d nginx postgres redis laravel-horizon

docker-compose exec workspace bash -c 'composer install; php artisan key:generate; php artisan migrate; php artisan l5-swagger:generate; vendor/bin/phpunit --configuration phpunit.xml'

