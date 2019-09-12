#!/bin/sh

if [ ! -f ./.env ]; then
    cp ./.env.example ./.env
fi

if [ ! -f ./docker/.env ]; then
    cp ./docker/env-example ./docker/.env
fi

cp ./docker/laravel-horizon/supervisord.d/laravel-horizon.conf.example ./docker/laravel-horizon/supervisord.d/laravel-horizon.conf
cd docker

docker-compose build nginx postgres redis laravel-horizon
docker-compose up -d nginx postgres redis laravel-horizon

docker-compose exec workspace bash -c 'composer install; php artisan key:generate; php artisan migrate; php artisan l5-swagger:generate; touch test.sqlite'
