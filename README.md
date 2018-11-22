# Playbook backend
[![pipeline status](https://gitlab.com/playbook-dev/backend/badges/master/pipeline.svg)](https://gitlab.com/playbook-dev/backend/commits/master)
[![coverage report](https://gitlab.com/playbook-dev/backend/badges/master/coverage.svg)](https://gitlab.com/playbook-dev/backend/commits/master)

### Initialization
* Install docker: `sudo snap install docker`
* Run initialization script: `./init.sh`

### Commands
* Update doc: `php artisan l5-swagger:generate`
* Enter to container: `cd docker && docker-compose exec workspace bash`
* Run tests: `vendor/bin/phpunit`
* Run sniffer: `php vendor/bin/phpcs ./app --standard=PSR2`

### Links
* [Swagger specification](https://swagger.io/docs/specification/about/)
