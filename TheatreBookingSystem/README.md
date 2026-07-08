# 🎭 Haraty Theatre Booking System

[![CI](https://github.com/hadimakki47/hadi-software-projects/actions/workflows/ci.yml/badge.svg)](https://github.com/hadimakki47/hadi-software-projects/actions/workflows/ci.yml)

A full web platform for browsing theatre shows and booking seats, with an admin panel and a built-in live support chat. Built as a Software Engineering course project with full requirements analysis, implementation, and automated testing.

## Features

**Customer side**
- Browse shows and showtimes with images, genres, and age ratings
- Interactive seat map (Premium / Regular / Economy rows priced with multipliers)
- **Atomic bookings** — booking row, seat details, and seat locks are one database transaction, with a guard that rejects double-booked seats and rolls everything back
- Coupon codes (percentage or fixed, with validity windows, minimum purchase, usage caps)
- Booking history, profile management, password change
- **Live support chat** widget with unread-message polling

**Admin panel**
- Dashboard with totals (shows, users, bookings) and revenue reports (by date range, by show)
- Manage shows, showtimes (auto-generates the 80-seat map per hall), users, coupons, and bookings
- Staff role with scoped permissions; answer customer support chats

## Tech stack

- **PHP 8** + **MySQL** — every query is a prepared statement, business rules live in a pure, unit-testable module ([includes/pricing.php](includes/pricing.php))
- Sessions + bcrypt (`password_hash`) authentication with session-ID regeneration on login; role-based access (user / staff / admin)
- **PHPUnit 11** test suite: unit tests for pricing/coupon rules + integration tests that exercise the booking flow against a real MySQL
- **GitHub Actions CI**: lint → unit tests → integration tests (MySQL service) → Docker smoke test
- Vanilla JS + AJAX for the chat widget and seat selection, custom CSS

## Run with Docker (recommended)

```bash
docker compose up
```

Open `http://localhost:8082`. The schema is imported automatically and two demo accounts are seeded:

| Account | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Staff | `staff` | `staff123` |

## Run the tests

```bash
composer install
composer test:unit          # pure business logic, no database needed
composer test:integration   # needs MySQL: set DB_HOST/DB_PORT/DB_USER/DB_PASS
composer test               # both
```

Integration tests self-skip when no database server is reachable. They provision their own schema in a separate `theatre_booking_test` database, so they never touch real data.

## Structure

```
TheatreBookingSystem/
├── index.php                 # landing page
├── pages/                    # customer pages (shows, seats, bookings, profile…)
│   └── admin/                # admin panel
├── includes/
│   ├── pricing.php           # pure business rules (unit-tested)
│   ├── functions.php         # data-access layer (prepared statements)
│   ├── auth.php              # session auth + roles
│   └── db_config.php         # env-configurable connection
├── api/                      # chat + support JSON endpoints
├── templates/                # shared header/footer/chat widget
├── tests/
│   ├── Unit/                 # PricingTest — 18 tests
│   └── Integration/          # BookingFlowTest — real-DB booking/coupon flows
├── database/db_init.sql      # full schema (12 tables) + seeded demo users
└── docker-compose.yml
```

## Run with MAMP/XAMPP

1. Copy this folder into your web root.
2. Create the schema with `database/db_init.sql`.
3. Defaults target MAMP (`127.0.0.1:8889`, root/root). Override with `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME` env vars if needed.
