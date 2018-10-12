# ISport backend

### Initialization
1. Init git submodules: `git submodule init`
2. Update git submodules: `git submodule update`
3. Create app env file: `cp .env.example .env`
4. Generate app keys: `php artisan key:generate`
5. Create container env file: `cd docker && cp env-example ./.env`
6. Build containers: `docker-compose build nginx postgres`
7. Run container: `docker-compose up -d nginx postgres`
8. Build frontend: `compile-fronetnd.sh`
9. Setup db connection in .env file
10. Apply migrations in workspace container
11. Generate api doc files: `php artisan api:generate --routePrefix="api/*" --noPostmanCollection`

### Commands:
1. Update doc: `php artisan api:update`
2. Enter to container: `cd docker && docker-compose exec workspace bash`
3. Run tests: `php vendor/bin/phpunit`
