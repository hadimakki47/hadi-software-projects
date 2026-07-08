# 🍲 Recipe App — REST Backend

PHP/MySQL REST backend for an Android recipe application. Users can sign up, log in, browse and search recipes, post reviews with star ratings, and manage their profile (including base64 image uploads for profile pictures and recipe photos).

> **Note:** this folder contains the API backend and database schema. The Android client (Java + XML, Android Studio) consumes these endpoints over HTTP.

## Tech stack

- **PHP 8** (mysqli, prepared statements everywhere)
- **MySQL / MariaDB**
- **PHP sessions** for authentication, `password_hash()` / `password_verify()` (bcrypt) for credentials

## API endpoints

| Endpoint | Method | Description |
|---|---|---|
| `Signup.php` | POST | Register (`username`, `Password`, `Email`) |
| `Login.php` | POST | Log in, starts a session |
| `logout.php` | POST | Destroy session |
| `session_check.php` | GET | Returns `{logged_in, user_id, username}` |
| `getAllRecipes.php` | GET | List all recipes |
| `getRecipe.php?id=` | GET | One recipe + author info |
| `searchRecipe.php?q=` | GET | Search by name or instructions |
| `addRecipe.php` | POST (JSON) | Create recipe (JSON body) |
| `add_recipe.php` | POST (form) | Create recipe with base64 image upload |
| `get_reviews.php?recipe_id=` | GET | Reviews for a recipe |
| `add_review.php` | POST | Add review (rating 1–5) |
| `get_profile.php` | POST | Fetch profile by username |
| `update_profile.php` | POST | Update own profile (session-authenticated) |
| `change_password.php` | POST | Change own password (session-authenticated) |
| `upload_profile_pic.php` | POST | Upload own profile picture (session-authenticated) |

## Run with Docker (recommended)

```bash
docker compose up
```

API is served at `http://localhost:8080` with the schema from `recipeapp.sql` imported automatically.

## Run with XAMPP/MAMP

1. Copy `recipebackend/` into your web root (e.g. `htdocs/recipebackend`).
2. Import `recipeapp.sql` via phpMyAdmin.
3. Optionally set `DB_HOST` / `DB_USER` / `DB_PASS` / `DB_NAME` / `DB_PORT` env vars — defaults are `localhost` / `root` / empty / `recipeapp` / `3306`.

## Security notes

- All queries use prepared statements (no string-built SQL).
- Passwords are stored as bcrypt hashes.
- Profile/password endpoints verify the session user matches the target account.
- Database errors are logged server-side and never echoed to clients.
