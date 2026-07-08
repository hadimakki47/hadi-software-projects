# Hadi Makki — Software Engineering Portfolio

[![CI](https://github.com/hadimakki47/hadi-software-projects/actions/workflows/ci.yml/badge.svg)](https://github.com/hadimakki47/hadi-software-projects/actions/workflows/ci.yml)

University-level software projects: web platforms, a mobile app backend, and database systems. Every project in this repo ships with its own README and a one-command Docker setup.

| Project | What it is | Stack |
|---|---|---|
| [🎭 Theatre Booking System](TheatreBookingSystem/) | Seat booking platform with transactional bookings, coupons, admin panel, revenue reports, live support chat — PHPUnit-tested with CI | PHP, MySQL, PHPUnit, JS/AJAX |
| [🌍 VolunteerConnect](VolunteerConnect/) | Web platform connecting volunteers to NGOs and community events, with favorites, search filters, and accounts | PHP (PDO), MySQL, jQuery |
| [🍲 Recipe App backend](RecipeApp/) | REST API for an Android recipe app: auth, recipes, reviews, image uploads | PHP, MySQL |
| [👨‍⚕️ Patient Medical Report System](https://github.com/Sami482005/Patient-Medical-Report) | Patient information, appointments, and prescriptions management (external repo, team project) | Java, SQL |

## Highlights

- **Tested & CI-verified**: the Theatre Booking System has a PHPUnit suite (unit + real-database integration tests) and a GitHub Actions pipeline that lints every project, runs the tests against MySQL, and smoke-tests the Docker stack on every push
- **Security-conscious PHP**: prepared statements everywhere, bcrypt password hashing, session-based authorization checks with session-ID regeneration on login, no error details leaked to clients
- **Transactional integrity**: theatre bookings insert the booking, its seat details, and seat locks atomically, with a double-booking guard that rolls the whole transaction back
- **Runs anywhere**: each project has a `docker-compose.yml` that boots PHP + MySQL and imports the schema automatically

```bash
cd TheatreBookingSystem && docker compose up   # http://localhost:8082
cd VolunteerConnect     && docker compose up   # http://localhost:8081
cd RecipeApp            && docker compose up   # http://localhost:8080 (API)
```

## About

I'm a Computer Science graduate. You can reach me at makkihadi2@gmail.com — more projects on [my GitHub profile](https://github.com/hadimakki47).
