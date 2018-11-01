# ISport backend

### Initialization
* Init git submodules: `git submodule init`
* Update git submodules: `git submodule update`
* Create app env file: `cp PROJECT_PATH/.env.example PROJECT_PATH/.env`
* Create container env file: `cp PROJECT_PATH/docker/env-example PROJECT_PATH/docker/.env`
* Install docker `sudo apt install docker.io`
* [Install docker compose](https://github.com/docker/compose/releases)
* Build containers from docker folder: `docker-compose build nginx postgres`
* Run container from docker folder: `docker-compose up -d nginx postgres`
* Build frontend from workspace container: `compile-fronetnd.sh`
* Setup db connection in `PROJECT_PATH/.env` file
* Generate app keys: `php PROJECT_PATH/artisan key:generate`
* Apply migrations in workspace container
* Install passport: `php artisan passport:install`
* Publish money config: `php artisan vendor:publish --tag=money`
* Generate api doc files: `php artisan api:generate --routePrefix="api/*" --noPostmanCollection`

### Commands
* Update doc: `php artisan l5-swagger:generate`
* Enter to container: `cd docker && docker-compose exec workspace bash`
* Run tests: `php vendor/bin/phpunit`
* Run sniffer: `php vendor/bin/phpcs ./app --standard=PSR2`

### Links
* [Swagger specification](https://swagger.io/docs/specification/about/)