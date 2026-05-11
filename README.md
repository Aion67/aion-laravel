# aion-laravel

Laravel based web application for pharmacy management.

## Prerequisites (local machine)

- **PHP 8.4+** with extensions: `mbstring`, `xml`, `curl`, `zip`, `pdo_sqlite` (or `pdo_mysql` if you use MySQL instead)
- **Composer 2**
- **Node.js 22+** and npm (for Vite / Tailwind)

Check versions:

```bash
php -v
composer --version
node -v
npm -v
```

## Local development (SQLite + `php artisan serve`)

This is the quickest way for teammates to run the app **without Docker**. The repo defaults to SQLite in `.env.example`.

1. **Clone and enter the project**

   ```bash
   git clone <repository-url> aion-laravel
   cd aion-laravel
   ```

2. **Environment file**

   ```bash
   cp .env.example .env
   ```

   Ensure database lines look like this (SQLite is already the default in `.env.example`):

   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```

   Set `APP_URL` to match how you will open the site, for example:

   ```env
   APP_URL=http://127.0.0.1:8000
   ```

3. **Create the SQLite database file**

   ```bash
   touch database/database.sqlite
   ```

4. **Install dependencies and bootstrap Laravel**

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   ```

5. **Frontend assets** (pick one)

   - **Development (hot reload):** run in a second terminal and leave it running:

     ```bash
     npm install
     npm run dev
     ```

   - **One-off build** (no separate Vite process):

     ```bash
     npm install
     npm run build
     ```

6. **Start the web server**

   ```bash
   php artisan serve
   ```

7. **Open the app**

   In a browser go to: **http://127.0.0.1:8000** (or the URL printed in the terminal).

**Optional seed data** (demo admin + pharmacist — change passwords after):

```bash
php artisan db:seed
```

**Run tests:**

```bash
composer test
```

### Local vs production `.env` (important)

- **Production (DigitalOcean, Docker Compose)** uses its own `.env` on the server. It is **not** the file on your laptop unless you SSH in and edit it there.
- **This repo does not commit `.env`** (see `.gitignore`). Pushing code to GitHub does **not** overwrite the server’s `.env` by default.
- For **SQLite + `php artisan serve`** on your machine, use a **local** `.env` derived from **`.env.example`**, not a copy of `.env.production.example` / not the server’s production file. Those are tuned for `DB_HOST=mysql` inside Docker, where the hostname `mysql` exists.

If you see **`getaddrinfo for mysql failed`** when running Artisan **on your laptop**, your local `.env` still points at MySQL/Docker. Fix it for local SQLite:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Then:

```bash
touch database/database.sqlite
php artisan config:clear
php artisan migrate
```

If you use **`npm run dev`**, set `APP_URL` in `.env` to the same base URL Laravel uses (e.g. `http://127.0.0.1:8000` when using `php artisan serve`) so Vite’s Laravel plugin matches; otherwise you may see wrong URLs in the console.

---

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
