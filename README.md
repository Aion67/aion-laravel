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
- Generate APP_KEY once and paste it into `.env`:
  - `docker compose -f docker-compose.prod.yml exec app php artisan key:generate --show`
- Run one-time setup after first start:
  - `docker compose -f docker-compose.prod.yml exec app php artisan migrate --force`
  - `docker compose -f docker-compose.prod.yml exec app php artisan config:cache`
  - `docker compose -f docker-compose.prod.yml exec app php artisan route:cache`
  - `docker compose -f docker-compose.prod.yml exec app php artisan view:cache`

## Auto Deploy on Merge to Main

This repository includes `.github/workflows/deploy.yml`, which deploys automatically after CI passes on `main`.

### 1) Server prerequisites

- Server has Docker + Docker Compose plugin installed.
- App is cloned on server, for example: `/var/www/aion-laravel`.
- Server has a valid production `.env` file at project root.
- `APP_KEY` is already set in that `.env`.

### 2) Required GitHub Secrets

In GitHub repository settings, add these Actions secrets:

- `DEPLOY_HOST`: server IP or hostname.
- `DEPLOY_USER`: SSH user on server.
- `DEPLOY_SSH_KEY`: private key used by GitHub Actions.
- `DEPLOY_PORT`: SSH port (usually `22`).
- `DEPLOY_PATH`: absolute path to project on server (example `/var/www/aion-laravel`).

### 3) Deploy behavior

On successful CI for `main`, deploy workflow will:

- fetch and reset server repo to `origin/main`
- run `docker compose -f docker-compose.prod.yml up -d --build`
- run `php artisan migrate --force`
- warm config, route, and view caches
