# Playbook backend

### Initialization
* Init git submodules: `git submodule init`
* Update git submodules: `git submodule update`
* Create app env file: `cp PROJECT_PATH/.env.example PROJECT_PATH/.env`
* Create container env file: `cp PROJECT_PATH/docker/env-example PROJECT_PATH/docker/.env`
* Install docker: `sudo snap install docker` 
* Build containers from docker folder: `docker-compose build nginx postgres`
* Run container from docker folder: `docker-compose up -d nginx postgres`
* **[In container]** Install dependencies `composer install`
* **[In container]** Build frontend from workspace container: `compile-fronetnd.sh`
* **[In container]** Setup db connection in `PROJECT_PATH/.env` file
* **[In container]** Generate app keys: `php PROJECT_PATH/artisan key:generate`
* **[In container]** Apply migrations `php artisan migrate`
* **[In container]** Install passport: `php artisan passport:install`
* **[In container]** Publish money config: `php artisan vendor:publish --tag=money`
* **[In container]** Generate api doc: `php artisan l5-swagger:generate`

### Commands
* Update doc: `php artisan l5-swagger:generate`
* Enter to container: `cd docker && docker-compose exec workspace bash`
* Run tests: `php vendor/bin/phpunit`
* Run sniffer: `php vendor/bin/phpcs ./app --standard=PSR2`

### Links
* [Swagger specification](https://swagger.io/docs/specification/about/)