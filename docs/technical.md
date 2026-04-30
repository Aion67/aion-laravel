# Technical Setup Guide (Full App)

This guide covers the full setup for `aion-laravel` on Linux, including:
- containerized setup (Laravel Sail + Docker)
- local setup (without Docker)
- Fedora and Debian/Kali command alternatives

## 1) Choose Your Setup Path

Use one of these paths:
- `Path A (recommended)`: Sail/Docker (best for dependency consistency)
- `Path B`: Local runtime (PHP/DB/Node installed directly on host)

---

## 2) Dependencies and Prerequisites

### A) Fedora/RHEL-based

```bash
sudo dnf update -y
sudo dnf install -y php-cli php-mbstring php-xml php-curl php-zip unzip git curl
sudo dnf install -y composer nodejs npm
sudo dnf install -y docker docker-compose-plugin
```

Optional fallback if legacy `docker-compose` is required:

```bash
sudo dnf install -y docker-compose
```

### B) Debian/Kali (APT-based)

```bash
sudo apt update
sudo apt install -y php-cli php-mbstring php-xml php-curl php-zip unzip git curl
sudo apt install -y composer nodejs npm
sudo apt install -y docker.io docker-compose-plugin
```

Optional fallback if `docker-compose` binary is required:

```bash
sudo apt install -y docker-compose
```

### Optional: Laravel Global Installer

```bash
composer global require laravel/installer
echo 'export PATH="$HOME/.config/composer/vendor/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

### Verify tools

```bash
php -v
composer --version
node -v
npm -v
docker --version
docker compose version
```

---

## 3) Docker Daemon + User Group Setup (Path A only)

### Start Docker service

Fedora:

```bash
sudo systemctl enable --now docker
```

Debian/Kali:

```bash
sudo systemctl enable --now docker
```

### Allow non-root Docker usage

```bash
sudo usermod -aG docker $USER
newgrp docker
```

Verify:

```bash
docker ps
groups
```

Notes:
- group changes do not apply to already-running shells
- if access still fails, log out and back in

---

## 4) Project Configuration Used in This Repo

Project path:

```bash
cd /home/winzer/PhpstormProjects/aion-laravel
```

Current project port values in `.env`:
- `APP_URL=http://localhost:8080`
- `APP_PORT=8080`
- `FORWARD_DB_PORT=3307`
- `VITE_PORT=5174`
- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_PORT=3306` (inside containers)

Port bindings in `compose.yaml`:
- App: `${APP_PORT:-80}:80`
- Vite: `${VITE_PORT:-5173}:${VITE_PORT:-5173}`
- MySQL: `${FORWARD_DB_PORT:-3306}:3306`
- Redis: `${FORWARD_REDIS_PORT:-6379}:6379`

---

## 5) Path A - Containerized Setup (Laravel Sail)

### 5.1 Install dependencies

```bash
cd /home/winzer/PhpstormProjects/aion-laravel
composer install
npm install
```

### 5.2 Start containers

```bash
./vendor/bin/sail down
./vendor/bin/sail up -d 
./vendor/bin/sail ps
```

### 5.3 App initialization inside container

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

Production assets:

```bash
./vendor/bin/sail npm run build
```

Open app:
- `http://localhost:8080`

### 5.4 SELinux nuance (mostly Fedora)

If you see container permission errors like:
- `Could not open input file: artisan`
- npm `EACCES` for `/var/www/html/package.json`

Ensure bind mounts include `:Z` in `compose.yaml` (already applied in this repo):
- `.:/var/www/html:Z`
- MySQL init script mount with `:ro,Z`

Optional relabel:

```bash
sudo restorecon -Rv /home/winzer/PhpstormProjects/aion-laravel
```

Debian/Kali usually do not require SELinux relabel steps unless SELinux is explicitly enabled.

---

## 6) Path B - Local Setup (No Docker)

### 6.1 Install dependencies

```bash
cd /home/winzer/PhpstormProjects/aion-laravel
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 6.2 Configure database in `.env`

Option 1: SQLite (quick local setup)

```dotenv
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Create SQLite file:

```bash
touch database/database.sqlite
```

Option 2: MySQL local service

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### 6.3 Run app locally

```bash
php artisan migrate
npm run dev
php artisan serve --host=127.0.0.1 --port=8000
```

Open app:
- `http://127.0.0.1:8000`

---

## 7) Common Errors and Fixes

### A) `permission denied while trying to connect to docker.sock`

Cause: user not in docker group or session not refreshed.

Fix:

```bash
sudo usermod -aG docker $USER
newgrp docker
docker ps
```

If still failing, log out and log back in.

### B) `docker-compose: command not found`

Cause: environment expects legacy binary but only Compose plugin exists.

Check plugin:

```bash
docker compose version
```

Install plugin/fallback:

Fedora:

```bash
sudo dnf install -y docker-compose-plugin docker-compose
```

Debian/Kali:

```bash
sudo apt install -y docker-compose-plugin docker-compose
```

### C) `failed to bind host port ... address already in use`

Cause: existing service already uses port (`80`, `3306`, `5173`, etc.).

Fix:
1. change `.env` forwarding ports (`APP_PORT`, `FORWARD_DB_PORT`, `VITE_PORT`)
2. recreate containers

```bash
./vendor/bin/sail down
./vendor/bin/sail up -d --force-recreate
```

Find port owners:

```bash
ss -ltnp | grep -E ':80|:8080|:3306|:3307|:5173|:5174|:6379'
```

### D) `Could not open input file: artisan` or npm `EACCES` in Sail

Cause: bind mount access denied (commonly SELinux labeling issue).

Fix:
- ensure `:Z` mount labels in `compose.yaml`
- restart/recreate Sail containers

### E) Frontend build error: unresolved import `./bootstrap` or `axios`

Fix:
- ensure `resources/js/bootstrap.js` exists
- install `axios`

```bash
npm install axios
npm run build
```

---

## 8) Debugging Commands

### Docker/systemd

```bash
systemctl status docker --no-pager
journalctl -u docker --no-pager -n 200
```

### Sail container status/logs

```bash
./vendor/bin/sail ps
./vendor/bin/sail logs -f
./vendor/bin/sail logs -f laravel.test
./vendor/bin/sail logs -f mysql
```

### SELinux denials (Fedora)

```bash
sudo ausearch -m avc -ts recent
```

---

## 9) Useful Sail Commands

```bash
./vendor/bin/sail up -d
./vendor/bin/sail down
./vendor/bin/sail restart
./vendor/bin/sail shell
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan test
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build
./vendor/bin/sail mysql
```


---

## 10) Current Known-Good State

- Laravel app initialized in this repository.
- Breeze authentication scaffolding installed.
- Sail setup works with custom ports (`8080`, `3307`, `5174`).
- SELinux mount labeling fix applied in `compose.yaml`.
- Frontend build path fixed (`resources/js/bootstrap.js` + `axios`).
