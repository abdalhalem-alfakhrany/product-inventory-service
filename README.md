# Product Inventory Microservice

A RESTful API built with Laravel 10 for managing products and stock levels. Includes Redis caching, soft deletes, stock threshold alerts, and a full Docker setup.

---

## Stack

- PHP 8.1 / Laravel 10
- PostgreSQL 15
- Redis
- Nginx
- Docker & Docker Compose V2

---

## Requirements

Before you start make sure you have installed:

- Docker
- Docker Compose V2 (`docker compose version` should return v2.x.x)

---

## Getting Started

### 1. Clone the repo

```bash
git clone https://github.com/your-username/product-inventory.git
cd product-inventory
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

### 3. Update your .env

Open `.env` and set these values:

```env
APP_NAME=ProductInventory
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=inventory
DB_USERNAME=postgres
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_CLIENT=predis
REDIS_CACHE_DB=1
CACHE_DRIVER=redis

QUEUE_CONNECTION=sync
```

### 4. Build and start the containers

```bash
docker compose up -d --build
```

This starts 4 containers: `app` (PHP-FPM), `nginx`, `postgres`, and `redis`.

### 5. Install dependencies

```bash
docker compose exec app composer install
```

### 6. Generate app key

```bash
docker compose exec app php artisan key:generate
```

### 7. Run migrations

```bash
docker compose exec app php artisan migrate
```

### 8. Seed the database (optional)

```bash
docker compose exec app php artisan db:seed
```

The API will be available at `http://localhost:8080`.

---

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `DB_HOST` | PostgreSQL host | `postgres` |
| `DB_PORT` | PostgreSQL port | `5432` |
| `DB_DATABASE` | Database name | `inventory` |
| `DB_USERNAME` | Database user | `postgres` |
| `DB_PASSWORD` | Database password | `secret` |
| `REDIS_HOST` | Redis host | `redis` |
| `REDIS_PORT` | Redis port | `6379` |
| `REDIS_CLIENT` | Redis client driver | `predis` |
| `REDIS_CACHE_DB` | Redis database index for cache | `1` |
| `CACHE_DRIVER` | Cache driver | `redis` |
| `QUEUE_CONNECTION` | Queue driver | `sync` |

---

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products` | List all products (paginated) |
| GET | `/api/products/{id}` | Get a single product |
| POST | `/api/products` | Create a product |
| PUT | `/api/products/{id}` | Update a product |
| DELETE | `/api/products/{id}` | Soft delete a product |
| POST | `/api/products/{id}/stock` | Adjust stock level |
| GET | `/api/products/low-stock` | List products below threshold |

### Query Parameters

| Parameter | Endpoint | Description | Default |
|---|---|---|---|
| `page` | GET `/api/products` | Page number | `1` |
| `per_page` | GET `/api/products` | Items per page | `15` |

### Response Format

```json
{
  "success": true,
  "data": {},
  "meta": {
    "pagination": {
      "total": 20,
      "per_page": 15,
      "current_page": 1,
      "last_page": 2,
      "next": "http://localhost:8080/api/products?page=2",
      "prev": null
    }
  }
}
```

---

## Running Tests

Tests use an in-memory SQLite database so no separate test database setup is needed. Make sure `phpunit.xml` has these environment overrides:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_DRIVER" value="array"/>
```

### Run All Tests

```bash
docker compose exec app ./vendor/bin/pest
```

### Run a Specific File

```bash
docker compose exec app ./vendor/bin/pest tests/Feature/ProductApiTest.php
```

### Run a Specific Test by Name

```bash
docker compose exec app ./vendor/bin/pest --filter "can list products"
```

### Run With Coverage

```bash
docker compose exec app ./vendor/bin/pest --coverage
```

### Run in Watch Mode (reruns on file change)

```bash
docker compose exec app ./vendor/bin/pest --watch
```

### Test Coverage

| Test | What it covers |
|---|---|
| Can list products with pagination | GET `/api/products` returns paginated response with correct meta |
| Returns correct records on page 2 | Pagination returns remaining records on subsequent pages |
| Can fetch a single product | GET `/api/products/{id}` returns correct product |
| Returns 404 for missing product | Non-existent UUID returns `success: false` and status 404 |
| Returns 422 for invalid UUID | Non-UUID string returns validation error |
| Can create a product | POST `/api/products` stores record and returns 201 |
| Returns 422 on duplicate SKU | Creating with existing SKU returns validation error |
| Can update a product | PUT `/api/products/{id}` updates record in database |
| Soft deletes a product | DELETE `/api/products/{id}` sets `deleted_at`, record stays in DB |
| Deleted product returns 404 | Fetching a soft deleted product returns 404 |
| Can adjust stock | POST `/api/products/{id}/stock` updates `stock_quantity` correctly |
| Stock cannot go below zero | Adjustment that would make stock negative is rejected |
| Stock alert event fires | Adjusting stock below threshold dispatches `StockBelowThreshold` event |
| Can list low stock products | GET `/api/products/low-stock` returns only products below threshold |

---

## API Documentation

Generate the Swagger docs:

```bash
docker compose exec app php artisan l5-swagger:generate
```

Then visit:

```
http://localhost:8080/api/documentation
```

---

## Useful Commands

```bash
# View logs
docker compose logs -f app

# Access the container shell
docker compose exec app bash

# Access PostgreSQL
docker compose exec postgres psql -U postgres -d inventory

# Access Redis CLI
docker compose exec redis redis-cli

# Clear all cache
docker compose exec app php artisan cache:clear

# Stop all containers
docker compose down

# Stop and remove volumes (wipes database)
docker compose down -v
```

---

## Architectural Decisions

**Repository Pattern** — The data access logic lives in dedicated repository classes behind an interface. This keeps controllers thin and makes the data layer easy to swap or test in isolation.

**Cache Invalidation** — The product listing endpoint is cached in Redis with a key per page and limit. Any write operation (create, update, delete, stock adjustment) flushes the relevant cache keys immediately. TTL is set to 5 minutes as a safety net.

**Soft Deletes** — Products are never hard deleted. The `deleted_at` timestamp is set instead, which keeps historical stock data intact and makes recovery straightforward.

**Stock Protection** — Stock adjustments that would push quantity below zero are rejected at the service layer before hitting the database.

**Stock Alerts** — A `StockBelowThreshold` event fires whenever a stock adjustment brings a product under its threshold. The listener handles the notification logic separately, so the core adjustment flow stays clean.

**Rate Limiting** — All API routes are covered by Laravel's built-in rate limiter, set to 60 requests per minute per IP.

**UUID v7** — Products use UUID v7 as the primary key. It is time-ordered which gives better index performance than UUID v4 on PostgreSQL.
