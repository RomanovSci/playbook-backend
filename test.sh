#!/bin/sh

rm -f test.sqlite
touch test.sqlite

cd docker && docker-compose exec workspace bash -c './vendor/bin/phpunit'
