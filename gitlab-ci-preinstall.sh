#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git -yqq

pecl install xdebug
docker-php-ext-install pcntl
docker-php-ext-enable xdebug

# Install phpunit, the tool that we will use for testing
curl --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-7.phar
chmod +x /usr/local/bin/phpunit

curl --location --output /usr/local/bin/composer https://getcomposer.org/download/1.7.3/composer.phar
chmod +x /usr/local/bin/composer

composer install
