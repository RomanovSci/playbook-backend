#!/bin/sh

cd ./docker && docker-compose up -d nginx postgres redis laravel-horizon
