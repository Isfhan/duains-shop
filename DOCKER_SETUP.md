# Local Development Setup Guide

This document outlines the steps to run the application locally using Docker (Laravel Sail) without requiring a local PHP or Composer installation on your host machine.

---

## Prerequisites

* **Docker Desktop** (Installed and actively running)
* **Git** (Git Bash, PowerShell, or WSL2 terminal)

---

## 1. Environment Configuration

Copy `.env.example` to create your local `.env` file if it doesn't already exist:

```bash
cp .env.example .env

```

Ensure your `.env` contains the following local Docker database configuration:

```env
APP_NAME=Duains
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=duains_shop
DB_USERNAME=sail
DB_PASSWORD=password

WWWGROUP=1000
WWWUSER=1000

```

---

## 2. Install Vendor Dependencies

If you don't have local PHP/Composer installed, run vendor installation via a temporary Docker container:

### Git Bash (Windows)

```bash
MSYS_NO_PATHCONV=1 docker run --rm -v "$(pwd -W):/var/www/html" -w //var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs

```

### PowerShell / Linux / macOS

```powershell
docker run --rm -v "${PWD}:/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs

```

---

## 3. Start Docker Containers

Boot up all services (Application runtime, MySQL, Mailhog, MinIO) in the background:

```bash
docker compose up -d

```

---

## 4. Database & Aimeos Initialization

Run the initialization commands inside the running `laravel.test` container:

```bash
# Generate application security key
docker compose exec laravel.test php artisan key:generate

# Build fresh database schema and run Aimeos migrations
docker compose exec laravel.test php artisan migrate:fresh
docker compose exec laravel.test php artisan aimeos:setup

# Create local administrator account
docker compose exec laravel.test php artisan aimeos:account --admin admin@duains.com

# Optimize and clear caches
docker compose exec laravel.test php artisan optimize:clear

```

---

## Local Service Endpoints

| Service | Local URL | Credentials / Notes |
| --- | --- | --- |
| **Frontend Web App** | `http://localhost` | Storefront |
| **Admin Panel** | `http://localhost/admin` | `admin@duains.com` |
| **Mailhog** | `http://localhost:8025` | Local email capture dashboard |
| **MinIO Console** | `http://localhost:8900` | User: `sail` | Pass: `password` |

---

## Useful Maintenance Commands

* **View Application Logs:** `docker compose logs -f laravel.test`
* **Stop All Containers:** `docker compose down`
* **Access Container Bash Terminal:** `docker compose exec laravel.test bash`
