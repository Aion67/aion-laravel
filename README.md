# aion-laravel

Laravel based web application for pharmacy management.

## Local Development (Sail)

- Copy `.env.sail.example` to `.env` and fill required values.
- Start local services with `./vendor/bin/sail up -d`.
- Initialize app with:
  - `./vendor/bin/sail artisan key:generate`
  - `./vendor/bin/sail artisan migrate`
  - `./vendor/bin/sail npm run dev`

## Production-style Docker

- Copy `.env.production.example` to `.env` and set strong secrets.
- Build and run with:
  - `docker compose -f docker-compose.prod.yml build`
  - `docker compose -f docker-compose.prod.yml up -d`
- Run one-time setup after first start:
  - `docker compose -f docker-compose.prod.yml exec app php artisan key:generate`
  - `docker compose -f docker-compose.prod.yml exec app php artisan migrate --force`
  - `docker compose -f docker-compose.prod.yml exec app php artisan config:cache`
  - `docker compose -f docker-compose.prod.yml exec app php artisan route:cache`
  - `docker compose -f docker-compose.prod.yml exec app php artisan view:cache`
