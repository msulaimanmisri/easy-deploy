# Default Commands

This document explains each default command executed during deployment and their purpose.

## composer install --no-dev --optimize-autoloader
Installs production dependencies and optimizes the autoloader for better performance.
* Why? Downloads only production packages (excludes dev dependencies) and creates optimized class maps.
* Benefit: Faster class loading and smaller footprint in production environments.

## php artisan migrate --force
Executes database migrations without requiring user confirmation.
* Why? Automates the migration process during deployment, applying database changes safely.
* Benefit: Ensures the database schema is always up to date with the codebase.

## php artisan clear-compiled
Removes the compiled class file used by Laravel.
* Why? Ensures Laravel uses fresh compiled files after deployment.
* Benefit: Prevents issues caused by stale compiled files during updates.

## php artisan optimize:clear
Clears all cached data in your Laravel application, including configuration, route, view, and application caches.
* Why? Simplifies the cache-clearing process by combining multiple cache commands (`config:clear`, `view:clear`, `route:clear`, etc.) into one.
* Benefit: Ensures the application runs with fresh cache files, preventing potential issues caused by stale or outdated caches while reducing redundancy in deployment scripts.

## php artisan storage:link
Creates symbolic links for public storage access.
* Why? Links the `storage/app/public` directory to `public/storage` for file accessibility.
* Benefit: Enables public access to uploaded files and assets stored in the storage directory.

## chmod -R 775 storage bootstrap/cache
Sets proper file permissions for storage and cache directories.
* Why? Ensures Laravel can read and write to critical directories for caching and file operations.
* Benefit: Prevents permission-related errors during application runtime.

## php artisan queue:restart
Restarts the queue worker processes.
* Why? Ensures that any changes to your codebase are reflected in the queue workers.
* Benefit: Prevents workers from running outdated code after deployment.

## php artisan optimize
Prepares the application by optimizing cache files for faster performance.
* Why? Combines and caches framework bootstrap files for better runtime efficiency.
* Benefit: Reduces overhead during application startup and improves response times.

---

## Command Execution Order

The commands are executed in a specific order to ensure smooth deployment:

1. **Pre-deployment**: Install dependencies and optimize autoloader
2. **Database**: Run migrations to update schema
3. **Cache clearing**: Clear old compiled files and caches
4. **Storage & permissions**: Set up file access and permissions
5. **Queue management**: Restart workers with fresh code
6. **Final optimization**: Cache configurations and routes for production

## Timeout Configuration

Each command has a default timeout of 300 seconds (5 minutes). You can modify this in the configuration file:

```php
'timeout' => 300,
```