# ISport backend

### Initialization
1. Init git submodules: `git submodule init`
2. Update git submodules: `git submodule update`
3. Create app env file: `cp PROJECT_PATH/.env.example PROJECT_PATH/.env`
4. Generate app keys: `php PROJECT_PATH/artisan key:generate`
5. Create container env file: `cp PROJECT_PATH/docker/env-example PROJECT_PATH/docker/.env`
6. Build containers from docker folder: `docker-compose build nginx postgres`
7. Run container from docker folder: `docker-compose up -d nginx postgres`
8. Build frontend from workspace container: `compile-fronetnd.sh`
9. Setup db connection in `PROJECT_PATH/.env` file
10. Apply migrations in workspace container
11. Install passport: `php artisan passport:install`
12. Publish money config: `php artisan vendor:publish --tag=money`
13. Generate api doc files: `php artisan api:generate --routePrefix="api/*" --noPostmanCollection`

### Commands
1. Update doc: `php artisan l5-swagger:generate`
2. Enter to container: `cd docker && docker-compose exec workspace bash`
3. Run tests: `php vendor/bin/phpunit`
4. Run sniffer: `php vendor/bin/phpcs ./app --standard=PSR2`

### Links
1. [Swagger specification](https://swagger.io/docs/specification/about/)