up:
	docker compose up -d

down:
	docker compose down

init:
	cp .env.dist .env
