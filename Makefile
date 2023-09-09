up:
	docker compose up -d

down:
	docker compose down

deploy:
	@git pull
	@docker compose up -f docker-compose-prod.yml -d --build

init:
	cp .env.dist .env
