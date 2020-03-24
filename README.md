# Playbook backend
![Master](https://travis-ci.org/RomanovSci/playbook-backend.svg?branch=master)

### Initialization
* Install docker: `sudo snap install docker`
* Run initialization script: `./init.sh`

### Commands
* Update doc: `php artisan l5-swagger:generate`
* Enter to container: `cd docker && docker-compose exec workspace bash`
* Run tests: `vendor/bin/phpunit`
* Run sniffer: `php vendor/bin/phpcs ./app --standard=PSR2`

### Links
* [Playbook API](http://13.48.6.243/api/documentation)
* [Swagger specification](https://swagger.io/docs/specification/about/)
