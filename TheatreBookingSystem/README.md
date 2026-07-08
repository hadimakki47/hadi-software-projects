# 🎭 Haraty Theatre Booking System

A full web platform for browsing theatre shows and booking seats, with an admin panel and a built-in live support chat. Built as a Software Engineering course project with full requirements analysis, implementation, and testing.

## Features

**Customer side**
- Browse shows and showtimes with images, genres, and age ratings
- Interactive seat map (Premium / Regular / Economy rows priced with multipliers)
- Bookings run in a **database transaction** — seats are only marked booked if the whole booking succeeds
- Coupon codes (percentage or fixed, with validity windows, minimum purchase, usage caps)
- Booking history, profile management, password change
- **Live support chat** widget with unread-message polling

**Admin panel**
- Dashboard with totals (shows, users, bookings) and revenue reports (by date range, by show)
- Manage shows, showtimes (auto-generates the seat map per hall), users, coupons, and bookings
- Staff role with scoped permissions; answer customer support chats

## Tech stack

- **PHP 8** + **MySQL** (mysqli, transactions)
- Sessions + bcrypt (`password_hash`) authentication, role-based access (user / staff / admin)
- Vanilla JS + AJAX for the chat widget and seat selection, custom CSS

## Structure

```
TheatreBookingSystem/
├── index.php                 # landing page
├── pages/                    # customer pages (shows, seats, bookings, profile…)
│   └── admin/                # admin panel
├── includes/                 # db config, auth, business logic (functions.php)
├── api/                      # chat + support JSON endpoints
├── templates/                # shared header/footer/chat widget
├── database/db_init.sql      # full schema (10 tables)
├── css/, js/, uploads/
└── docker-compose.yml
```

## Run with Docker (recommended)

```bash
docker compose up
```

Open `http://localhost:8082`. The schema is imported automatically. Register a user, then promote it to admin:

```sql
UPDATE users SET role='admin' WHERE username='<your username>';
```

## Run with MAMP/XAMPP

1. Copy this folder into your web root.
2. Create the schema with `database/db_init.sql` (or run `database/db_setup.php`).
3. Defaults target MAMP (`127.0.0.1:8889`, root/root). Override with `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME` env vars if needed.
