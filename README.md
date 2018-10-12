# ISport backend

### Initialization
1. Init git submodules: `git submodule init`
2. Update git submodules: `git submodule update`
3. Copy container config: `cd docker && cp env-example ./.env`
4. Build containers: `docker-compose build nginx postgres`
5. Run container: `docker-compose up -d nginx postgres`
6. Build frontend: `compile-fronetnd.sh`
7. Apply migrations in workspace container

### Commands:
1. Generate doc: `php artisan api:generate --routePrefix="api/*" --noPostmanCollection`
2. Update doc: `php artisan api:update`
3. To container: `cd docker && docker-compose exec workspace bash`