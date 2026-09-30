# Speedex

Speedex is a multi-tenant delivery and logistics management platform built with [Laravel](https://laravel.com). It lets delivery companies manage their day-to-day operations end to end: from couriers, vehicles, cities and delivery zones, to clients, orders, payments and stock, all behind role-based dashboards.

The platform runs as a **SaaS**: a Super Admin onboards client companies, sells them subscriptions and collects payments via [Monetbil](https://www.monetbil.com) (Mobile Money / Orange Money). Each subscribed client then gets its own delivery workspace.

## Features

### Super Admin (platform operator)

- Manage client companies (create, edit, activate/deactivate, recap).
- Manage subscription plans (`abonnement`): duration (hour/day/week/month/year), price, grace period.
- Manage payment APIs (e.g. Monetbil credentials).
- Track subscription transactions and payments, with Monetbil payment verification.
- Error pages for inactive / expired / unpaid client subscriptions.

### Admin / Agent (delivery workspace)

- Manage users, clients, agents and user types, and link them to accounts.
- Manage geography: cities, zones, neighborhoods (`quartier`) and delivery-zone pricing (`montant_livraison`).
- Manage couriers and assign them to delivery zones.
- Manage boutiques, relay points (`point_relais`), products and stock.
- Manage orders (`commandes`): create orders, link boutiques, choose pickup/delivery locations, compute amounts, and assign couriers.
- Track deliveries and delivery fees, and manage notifications.

### Couriers

- View assigned and in-progress orders by date.
- Record activity and update delivery status (delivered, cancelled).
- Manage own account, clients and orders.

### Clients

- Place orders (individual or corporate) and track their status.
- Manage their account and couriers.

### Multi / fleet management

- Manage vehicle types and vehicles (registration, model, city, courier assignment).
- Manage couriers and delivery zones centrally.

## Roles

| Role            | Dashboard route   | Purpose                                   |
| --------------- | ----------------- | ----------------------------------------- |
| Super Admin     | `superadmin`      | Platform / subscription / billing admin   |
| Agent (routeur) | `admin`           | Dispatch and workspace administration     |
| Courier         | `coursier`        | Delivery execution                        |
| Client          | `client`          | Order placement and tracking              |

## Tech stack

- **Backend:** PHP `^8.1`, Laravel `^10.10`, Laravel Sanctum, Laravel UI
- **Frontend:** Vue 3, Bootstrap 5, Vite, Sass
- **Payments:** Monetbil (Mobile Money / Orange Money, XAF)

## Prerequisites

- PHP `>= 8.4` (the locked dependencies require it) with the usual Laravel extensions
- Composer
- Node.js and npm
- SQLite for local development, MySQL/MariaDB in production

## Installation (development)

```bash
# 1. Install PHP dependencies
composer install

# 2. Create the environment file (SQLite by default)
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

# 3. Install and build frontend assets
npm install
npm run build

# 4. Create the schema and a development dataset
php artisan migrate:fresh --seed

# 5. Serve the application
php artisan serve
```

The development seeder (refused in production) creates the reference data, an
active subscription and one account per role: `admin@example.com`,
`agent@example.com`, `coursier@example.com`, `client@example.com`, plus
`superadmin@example.com` for `/superadmin`. They share the password set in
`SEED_PASSWORD`, or a generated one printed by the seeder.

## Quality checks

```bash
./vendor/bin/phpunit          # tests
./vendor/bin/pint --test      # code style
./vendor/bin/phpstan analyse  # static analysis
```

## Deployment

See `docs/DEPLOIEMENT_PREPROD.md` (shared hosting, `preprod` branch).

## Notes

- Delivery orders follow a status lifecycle: `attente` → `attribue` → `encours` → `livre` / `annulee` / `echoue`.
- Delivery fees are configured per pickup zone / delivery zone pair.
- Subscription access is enforced by middleware (`Check_Sa_Client_Error`) that validates client status and subscription validity.
