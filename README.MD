# SCORM Demo (Laravel + Filament + Horizon)
A Laravel 12 application for uploading, extracting, and viewing SCORM packages with basic user-level statistics (view count and last viewed date).

## Tech Stack
PHP 8.4 (FPM, Alpine)
- **Laravel 12**
- **MySQL 8.4** 
- **Redis** (queues, cache)
- **Laravel Horizon** (queue workers and dashboard)
- **Filament Admin** (admin panel)
- **Nginx** (port 8000)
- **Docker Compose**

## Containers
- **nginx** - frontend (port 8000)
- **backend** - PHP-FPM + Supervisor (php-fpm + horizon workers)
- **mysql** - MySQL 8.4 (persistent volume `./db_data`)
- **redis** - Redis (alpine)

## Quick Start
### 1. Clone the repository

```bash
  git clone <your-repo-url> scorm-demo
```  
```bash
  cd ScormTracker
```

### 2. Prepare environment
Copy the example .env:
```bash
  cp backend/.env.example backend/.env
```

Edit backend/.env as needed:
```dotenv
APP_URL=http://localhost:8000
DB_HOST=mysql
DB_DATABASE=scorm_db
DB_USERNAME=scorm_user
DB_PASSWORD=secret

QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=redis

# SCORM storage configuration
SCORM_DISK=public
SCORM_PATH=scorm
```

### 3. Build and start containers
```bash
  docker compose up -d --build
```

### 4. Install dependencies & run migrations
```bash
  docker compose exec backend php artisan migrate
```
```bash
  docker compose exec backend php artisan storage:link
```

### 5. Create an admin user
**Option A - Seeder**
The `DatabaseSeeder` calls `FilamentAdminSeeder` and creates `admin@example.com / password`:
```bash
  docker compose exec backend php artisan db:seed
```

**Option B – Manually via tinker**
```bash
  docker compose exec backend php artisan tinker
  >>> \App\Models\User::create([
  ...   'name' => 'Admin',
  ...   'email' => 'admin@example.com',
  ...   'password' => bcrypt('password'),
  ...   'is_admin' => true,
  ... ]);
```

## 5. Access the application
- **Frontend (SCORM list)** - `http://localhost:8000/scorm`
_(requires authentication - log in via Filament first)_
- **Filament Admin Panel** — `http://localhost:8000/admin`
Use the admin credentials created in step 5.
- **Horizon Dashboard** - `http://localhost:8000/horizon`

## SCORM Workflow
1. **Upload** - Upload a `.zip` SCORM package in the Filament admin panel.
2. **Queue** - The `ExtractScormZipJob` is dispatched, moving the file to `/storage/app/public/tmp/*.zip`.
3. **Extraction** - The archive is extracted to `/storage/app/public/scorm/{id}/`.
If the folder exists, it’s removed first.
4. **Viewing** - On the frontend `/scorm,` the "Start" button opens the SCORM content inside an `<iframe>` with a mock SCORM 1.2/2004 API.
5. **Statistics** - Each time a user opens a package, a record in `scorm_user_stats` is updated:
   - `views_count` is incremented
   - `last_viewed_at` is updated

## Routes
**Frontend**
```routes
GET /scorm                      // List packages
GET /scorm/{scorm}              // Show SCORM player page
GET /scorm/{scorm}/file/{path}  // Serve extracted SCORM files
```

**Admin**
```routes
GET /admin
```

- CRUD for `ScormPackage`
- ZIP upload form 
- Dispatches extraction job

**Horizon**
```routes
  GET /horizon
```
Queue monitoring dashboard.

## Useful Commands
```bash
  # Start services
  docker compose up -d
```
```bash
  # Stop services
  docker compose down
```
```bash
  # Access backend container
  docker compose exec backend sh
```
```bash
  # Inside backend container:
  php artisan migrate
  php artisan db:seed
  php artisan tinker
```
```bash
  # Logs
  docker compose logs -f backend
  docker compose logs -f nginx
```

## File Storage
- Temporary ZIP files: `storage/app/public/tmp/*.zip` 
- Extracted SCORM content: `storage/app/public/scorm/{id}/...` 
- Configurable in `config/scorm.php` via `SCORM_DISK` and `SCORM_PATH`. 
- Files are served via `ScormFileController@show` with correct MIME types, CSP, and caching headers.

## Troubleshooting
### Blank iframe / 404 assets
- Check if the extraction job completed in Horizon. 
- Check `storage/logs/laravel.log` for `ExtractScormZipJob`. 
- Confirm the files exist in `storage/app/public/scorm/{id}/`.

### Extraction job not running
- Ensure Redis is running and Horizon is up. 
- Check logs:
```
docker compose logs -f backend
```
```
storage/logs/laravel.log
```

## "Not a zip archive" error
- Verify you’re uploading a valid `.zip`. 
- Make sure PHP `zip` extension is enabled (it is in this Docker image).