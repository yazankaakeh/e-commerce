<h1 align="center">Contrast</h1>

<p align="center">
  An open-source Laravel eCommerce platform built on
  <a href="https://laravel.com/">Laravel 12</a> and
  <a href="https://vuejs.org/">Vue.js 3</a>.
</p>

---

## Table of Contents

- [About Contrast](#about-contrast)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Installation Guide (Windows / Laragon)](#installation-guide-windows--laragon)
  - [1. Install Laragon](#1-install-laragon)
  - [2. Install Composer](#2-install-composer)
  - [3. Install Node.js & npm](#3-install-nodejs--npm)
  - [4. Install Git](#4-install-git)
  - [5. Verify everything is installed](#5-verify-everything-is-installed)
  - [6. Clone the project](#6-clone-the-project)
  - [7. Configure environment](#7-configure-environment)
  - [8. Create the database](#8-create-the-database)
  - [9. Install PHP dependencies](#9-install-php-dependencies)
  - [10. Run the Contrast installer](#10-run-the-contrast-installer)
  - [11. Build front-end assets](#11-build-front-end-assets)
  - [12. Start the development server](#12-start-the-development-server)
- [Installation Guide (macOS / Linux)](#installation-guide-macos--linux)
- [Default Admin Credentials](#default-admin-credentials)
- [Common Commands](#common-commands)
- [Project Structure](#project-structure)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## About Contrast

Contrast is a feature-rich, modular, open-source eCommerce platform. It ships with a multi-store admin panel, a customer-facing storefront, multi-currency and multi-locale support, a flexible attribute and product system, and a plugin architecture you can extend without modifying core code.

## Tech Stack

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Backend     | PHP 8.3+, Laravel 12                |
| Frontend    | Vue.js 3, Tailwind CSS 3, Vite 5    |
| Database    | MySQL 8.0+ / MariaDB 10.6+          |
| Testing     | Pest 3 (PHP), Playwright (E2E)      |
| Tooling     | Composer 2, Node.js 18+, npm        |

## System Requirements

Before installing Contrast you need the following on your machine:

- **PHP 8.3 or higher** with these extensions enabled: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`, `exif`
- **MySQL 8.0+** (or MariaDB 10.6+)
- **Composer 2.x**
- **Node.js 18+** and **npm 9+**
- **Git**
- A web server (Apache or Nginx) — Laragon provides this on Windows

---

## Installation Guide (Windows / Laragon)

This is the recommended path on Windows. Laragon bundles Apache, Nginx, MySQL, and PHP into one easy-to-manage stack.

### 1. Install Laragon

Laragon is a portable, isolated Windows development environment that bundles Apache/Nginx, MySQL, PHP, and a friendly UI.

1. Download **Laragon Full** from the official site: <https://laragon.org/download/>
2. Run the installer and accept the defaults. Laragon installs to `C:\laragon` by default.
3. Open Laragon and click **Start All**. You should see Apache and MySQL turn green.
4. Right-click the Laragon window → **PHP → Version** and confirm **PHP 8.3** (or newer) is selected. If it isn't, download a PHP 8.3 build, drop it into `C:\laragon\bin\php\`, then pick it from the menu.
5. Right-click the Laragon window → **MySQL → Version** and confirm MySQL 8.0+ is selected.

> Tip: enable **Auto Virtual Hosts** under **Menu → Preferences** so Laragon automatically wires up a `*.test` domain for any folder you create in `C:\laragon\www\`.

### 2. Install Composer

Composer is the PHP dependency manager. Contrast uses it to install Laravel and all of Contrast's modular packages.

1. Download the Windows installer: <https://getcomposer.org/Composer-Setup.exe>
2. Run it. When asked, point it at the PHP binary Laragon uses, e.g. `C:\laragon\bin\php\php-8.3.x-Win32-vs16-x64\php.exe`.
3. Tick **Add to PATH** and finish the installer.
4. Open a new terminal (Laragon → **Menu → Terminal**) and run:

   ```bash
   composer --version
   ```

   You should see `Composer version 2.x.x`.

### 3. Install Node.js & npm

Node.js powers the Vite build that produces the admin and shop front-end bundles.

1. Download the **LTS** installer (18.x or newer) from <https://nodejs.org/>
2. Run it with default settings — npm is bundled.
3. Verify in a terminal:

   ```bash
   node --version
   npm --version
   ```

### 4. Install Git

Git is required to clone the project and pull updates.

1. Download Git for Windows: <https://git-scm.com/download/win>
2. Run the installer (defaults are fine).
3. Verify:

   ```bash
   git --version
   ```

### 5. Verify everything is installed

Open a fresh terminal and run all four checks:

```bash
php --version       # should print PHP 8.3.x or newer
composer --version  # 2.x.x
node --version      # v18 or newer
git --version       # any recent version
```

If any command says "not found", reopen the terminal — installers often need a restart to update `PATH`.

### 6. Clone the project

If you haven't cloned the repo yet:

```bash
cd C:\laragon\www\laravel
git clone <your-repo-url> e-commerce
cd e-commerce
```

If the project is already in `C:\laragon\www\laravel\e-commerce`, just open Laragon → **Menu → www** and navigate into it.

### 7. Configure environment

Create your local `.env` file from the template:

```bash
copy .env.example .env
```

Open `.env` and update at least these entries:

```ini
APP_NAME=Contrast
APP_URL=http://e-commerce.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contrast
DB_USERNAME=root
DB_PASSWORD=
```

Laragon's default MySQL user is `root` with **no password**.

### 8. Create the database

Open Laragon → **Menu → MySQL → HeidiSQL** (or use any MySQL client) and run:

```sql
CREATE DATABASE contrast CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or via the command line:

```bash
mysql -u root -e "CREATE DATABASE contrast CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 9. Install PHP dependencies

From the project folder (`C:\laragon\www\laravel\e-commerce`):

```bash
composer install
```

This pulls in Laravel, all of Contrast's modular packages (located under `packages/`), and dev tools like Pest. Expect 2–5 minutes on a fresh install.

### 10. Run the Contrast installer

Contrast ships with a one-shot artisan command that runs migrations, seeds the catalog/locale defaults, generates an app key, and publishes assets:

```bash
php artisan contrast:install
```

You'll be prompted for a default admin email and password during the run.

> **Note:** Some legacy artisan commands still use the upstream framework name. If `php artisan contrast:install` is not yet registered in your build, run `php artisan bagisto:install` instead — it executes the same routine. The same applies to `bagisto:translations:check`. These will be aliased to `contrast:*` in a future cleanup pass.

### 11. Build front-end assets

Each of the three front-end bundles (Admin, Shop, Installer) has its own Vite build. Build them once for production, or run `dev` for hot-reloading while developing.

```bash
# Admin panel
cd packages/Webkul/Admin
npm install
npm run build      # or: npm run dev

# Storefront
cd ../Shop
npm install
npm run build      # or: npm run dev

# Installer (only needed if you customize the install wizard)
cd ../Installer
npm install
npm run build
```

Outputs land in `public/themes/admin/default/build/` and `public/themes/shop/default/build/`.

### 12. Start the development server

From the project root:

```bash
php artisan serve
```

Open <http://127.0.0.1:8000> for the storefront and <http://127.0.0.1:8000/admin/login> for the admin panel.

If you have Laragon's auto virtual hosts enabled, you can also visit <http://e-commerce.test> directly.

---

## Installation Guide (macOS / Linux)

The dependency list is the same — you just install each piece through your platform's package manager:

```bash
# macOS (Homebrew)
brew install php@8.3 composer node mysql git
brew services start mysql

# Ubuntu / Debian
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
                    php8.3-mysql php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath \
                    composer nodejs npm mysql-server git
```

Then follow steps 6–12 above. Use `cp .env.example .env` instead of `copy`.

---

## Default Admin Credentials

After running the installer, the default admin login is:

- **URL:** <http://127.0.0.1:8000/admin/login>
- **Email:** `admin@example.com`
- **Password:** `admin123`

Change these immediately in **Settings → Users** after first login.

---

## Common Commands

```bash
# Clear all caches (run after config or code changes)
php artisan optimize:clear

# Run the test suite (Pest)
vendor/bin/pest
vendor/bin/pest --testsuite="Admin Feature Test"

# Check / fix code style (Laravel Pint)
vendor/bin/pint --test
vendor/bin/pint

# Translation file validation
php artisan contrast:translations:check    # falls back to: bagisto:translations:check

# Re-seed the database (destructive — drops & recreates)
php artisan migrate:fresh --seed
```

### End-to-end (Playwright) tests

Run from each package's directory:

```bash
cd packages/Webkul/Admin
npm install
npx playwright install --with-deps chromium
npx playwright test --config=tests/e2e-pw/playwright.config.ts
```

Repeat for `packages/Webkul/Shop` if needed. Playwright tests require `php artisan serve` running and a seeded database.

---

## Project Structure

```
e-commerce/
├── app/                      # Laravel application skeleton
├── bootstrap/providers.php   # Service provider registry
├── config/concord.php        # Module/concord registry
├── packages/Webkul/          # ~42 modular packages (Admin, Shop, Catalog, ...).
│                             # The "Webkul" path is a legacy PHP namespace —
│                             # renaming it would break Composer autoloading.
│   └── <Package>/src/
│       ├── Config/           # admin-menu.php, acl.php, system.php
│       ├── Database/         # Migrations, seeders, factories
│       ├── Http/Controllers/ # Admin/ and Shop/ controllers
│       ├── Models/           # Eloquent models + Proxy classes
│       ├── Repositories/     # Data access layer
│       ├── Resources/        # Views, lang, assets
│       └── Routes/           # admin-routes.php, shop-routes.php
├── public/                   # Web root, compiled assets, uploads
├── resources/                # Top-level Laravel resources
├── routes/                   # Top-level route files
├── tests/                    # Pest test bootstrap
└── README.md
```

Each package is self-contained: its own routes, views, migrations, and service providers. Add a new package with:

```bash
php artisan package:make Webkul/<Name>
```

Then register it in `bootstrap/providers.php` and `config/concord.php`.

---

## Troubleshooting

**`php` is not recognised after installing Laragon.**
Add `C:\laragon\bin\php\php-8.3.x-Win32-vs16-x64` to your `PATH`, or always run commands from the Laragon terminal (Menu → Terminal), which has `PATH` pre-set.

**`Class "ZipArchive" not found` during `composer install`.**
The `php_zip` extension is disabled. Open `php.ini` (Laragon → **Menu → PHP → php.ini**), uncomment `extension=zip`, then restart Laragon.

**`SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'`.**
Laragon defaults to MySQL root with no password. Set `DB_PASSWORD=` (empty) in `.env`. If you've set a password, put it there.

**Vite build fails with an out-of-memory error.**
Bump Node's heap: `set NODE_OPTIONS=--max-old-space-size=4096` (Windows) or `export NODE_OPTIONS=--max-old-space-size=4096` (mac/Linux), then re-run `npm run build`.

**Admin panel loads but styles are missing.**
You forgot step 11 — run `npm run build` inside `packages/Webkul/Admin` and `packages/Webkul/Shop`.

**Permission errors when writing to `storage/` or `bootstrap/cache/` (Linux/macOS).**

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

---

## License

Contrast is open-source software released under the [MIT License](LICENSE).
