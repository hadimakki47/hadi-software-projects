# 🌍 VolunteerConnect

A web platform that connects volunteers with NGOs and community events. Visitors can browse and filter volunteer opportunities; registered users can favorite opportunities, manage a profile, subscribe to a newsletter, and contact the team.

## Features

- **Browse & search opportunities** — filter by keyword, category, and location (AJAX, no page reloads)
- **Accounts** — signup/login with bcrypt-hashed passwords and PHP sessions
- **Favorites** — logged-in users can toggle favorites, persisted per user
- **Contact form & newsletter** — AJAX submissions with client- and server-side validation
- **Responsive UI** — mobile navigation, custom CSS (no framework)

## Tech stack

- **PHP 8** with **PDO** (prepared statements everywhere)
- **MySQL**
- **jQuery / AJAX** front end, semantic HTML + custom CSS

## Structure

```
VolunteerConnect/
├── index.php, opportunities.php, about.php, contact.php,
│   login.php, signup.php, profile.php     # server-rendered pages
├── api/                                   # JSON/AJAX endpoints
│   ├── config.php          # PDO connection (env-configurable)
│   ├── login.php, signup.php, logout.php
│   ├── get_opportunities.php, toggle_favorite.php
│   ├── contact_submit.php, subscribe.php
├── css/, js/, images/
└── volunteer_connect.sql   # schema + seed data
```

## Run with Docker (recommended)

```bash
docker compose up
```

Open `http://localhost:8081`. The schema in `volunteer_connect.sql` is imported automatically.

## Run with XAMPP/MAMP

1. Copy this folder into your web root.
2. Import `volunteer_connect.sql` via phpMyAdmin.
3. Set `DB_HOST` / `DB_PORT` / `DB_USER` / `DB_PASS` / `DB_NAME` env vars if your setup differs from the defaults (`127.0.0.1:3307`, `root`, no password).

## Security notes

- All SQL uses PDO prepared statements.
- Passwords stored with `password_hash()` (bcrypt).
- Server-side validation on all write endpoints; DB errors are logged, not exposed to clients.
