# FlipPlay REST API

Demo REST API for the FlipPlay media catalogue, built with Laravel 12, Eloquent, and SQLite. Media types follow the Module 3 categories: `anime`, `drama`, `manga`, and `novel`.

## Run locally

Requirements: PHP 8.2+, Composer, and the PHP `pdo_sqlite` and `fileinfo` extensions.

```powershell
cd D:\pemweblab\flipplay-api
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite }
php artisan key:generate
php artisan migrate
php artisan route:list --path=api
php artisan serve
```

The API is available at `http://127.0.0.1:8000/api`. Import `FlipPlay.postman_collection.json` in Postman to run the happy-path and failure requests in order.

## Endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/media?search={title}&include_users=true` | List/search media (15 per page); optionally include collection owners |
| POST | `/media` | Create media |
| GET | `/media/{media}` | Show media and its collection owners |
| PUT/PATCH | `/media/{media}` | Update media |
| DELETE | `/media/{media}` | Delete media (also removes pivot rows) |
| POST | `/users` | Create a collection owner |
| GET | `/users/{user}/collection` | List a user's media collection |
| POST | `/users/{user}/collection/{media}` | Add media to a collection; returns `409` if already present |
| DELETE | `/users/{user}/collection/{media}` | Remove media; returns `404` if it was not present |

Media JSON fields are `title`, `type`, `genre`, `year`, and optional `description`. Titles are unique; invalid request bodies return Laravel's JSON `422` validation response. Media and user resources include relationship fields only when those relationships are loaded.

Create media example:

```json
{
  "title": "Blue Horizon",
  "type": "anime",
  "genre": "Adventure",
  "year": 2024,
  "description": "A sample title"
}
```

## Testing

Run the API feature tests against an in-memory SQLite database:

```powershell
php artisan test
```

The feature tests cover all CRUD methods, title filtering and uniqueness validation, user creation, relationship serialization, and adding/removing collection entries including duplicate and missing-item errors.
