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

- **Backend:** PHP `^8.2`, Laravel `^12`, Laravel Sanctum, Laravel UI
- **Frontend:** Vue 3, Bootstrap 5, Vite, Sass
- **Payments:** Monetbil (Mobile Money / Orange Money, XAF)

## Prerequisites

- PHP `>= 8.2` with the required extensions (see Laravel 12 requirements)
- Composer
- Node.js and npm
- A relational database (MySQL/MariaDB, PostgreSQL, SQLite, or SQL Server)

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create the environment file and set your database credentials
cp .env.example .env
php artisan key:generate

# 3. Install and build frontend assets
npm install
npm run build

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Serve the application
php artisan serve
```

Seeders are idempotent. `ReferentielSeeder` creates the user types, the client
types and the base cities (Douala, Yaoundé); `SuperAdminSeeder` creates the
platform operator account (`superadmin@example.com` / `11111111`), the Monetbil
API row and the contact details. In `local` and `testing` environments only,
`DemoSeeder` adds an active licence and demo accounts, all with the password
`11111111`: `test@example.com` (admin), `agent@example.com`,
`coursier@example.com` and `client@example.com`.

Run the test suite with `php artisan test` (sqlite in memory, no setup needed).

## Multi-tenant roadmap

The platform is being transformed into a true multi-tenant SaaS where several
delivery companies sign up on their own. The detailed plan, phase by phase, is
in [`docs/plan-multi-tenant.md`](docs/plan-multi-tenant.md).

## Notes

- Delivery orders follow a status lifecycle: `attente` → `attribue` → `encours` → `livre` / `annulee` / `echoue`.
- Delivery fees are configured per pickup zone / delivery zone pair.
- Subscription access is enforced by middleware (`Check_Sa_Client_Error`) that validates client status and subscription validity.
