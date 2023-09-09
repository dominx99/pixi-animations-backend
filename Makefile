up:
	docker compose up -d

down:
	docker compose down

clear:
	docker compose exec tilesets_php bin/console cache:clear

bash:
	docker compose exec tilesets_php bash

deploy:
	@git pull
	@docker compose -f docker-compose.prod.yml up -d --build

init:
	cp .env.dist .env
