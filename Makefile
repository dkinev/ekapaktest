up:
	docker-compose up -d --build

down:
	docker-compose down

bash:
	docker exec -it app bash

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed

seed-test:
	docker-compose exec app php artisan db:seed --env=testing

migrate-test:
	docker-compose exec app php artisan migrate --env=testing

migrate-all:
	make migrate
	make migrate-test

seed-all:
	make seed
	make seed-test

test:
	docker-compose exec app php artisan test

cache-clear:
	docker-compose exec app php artisan cache:clear
