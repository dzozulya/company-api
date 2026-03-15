# LARAVEL COMPANY API Test Task (PHP 8.4 + Docker)

## Stack
- PHP 8.4 (FPM) + Composer
- Nginx
- PostgreSQL 14
- laravel 12


---
## Requirements
- Linux Bash
- Docker + Docker Compose

## Quick Start (step-by-step)

### 1) Clone & create env file
```bash
git clone git@github.com:dzozulya/company-api.git
cd company-api
cp .env.example .env

```
### 2) Build and start containers
```bash
docker compose up -d --build
```
### 3) Install dependencies
```bash
docker compose exec app composer install
```

### 4) Set application key
```bash
docker compose exec app php artisan key:generate
```

### 5) Run migrations
```bash
docker compose exec app php artisan migrate
```


### 6) Use app

* [Base URL:http://localhost:8080](http://localhost:8080/).

## API ENDPOINTS

* ```POST /api/company``` - create new company 
* ```GET /api/company/{edrpou}/versions``` - get all company versions by EDRPOU

### 7) Run Test
```bash
docker compose exec app php artisan test
```
### Possible Improvements

The current implementation focuses on solving the task requirements while keeping the code simple and readable.
In a production environment the following improvements could be implemented:

1. **Idempotency support**
    - Prevent duplicate requests using an `Idempotency-Key` header.
    - Store request results and return the same response for repeated requests.

2. **Improved versioning audit**
    - Store the difference between versions instead of full snapshots.
    - Track which fields were modified.

3. **Pagination**
    - Paginate version history if the number of versions grows large.

4. **Caching**
    - Cache company data and version lists for frequently accessed records.
