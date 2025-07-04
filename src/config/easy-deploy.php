<?php

declare(strict_types=1);

return [
    /**
     * Deployment Commands
     * Define the commands to be executed during deployment.
     * Add your desired commands or remove existing commands as needed.
     */
    'commands' => [
        // Pre-deployment
        'composer install --no-dev --optimize-autoloader',

        // Database
        'php artisan migrate --force',

        // Cache clearing
        'php artisan clear-compiled',
        'php artisan optimize:clear',

        // Storage & permissions
        'php artisan storage:link',
        'chmod -R 775 storage bootstrap/cache',

        // Queue & jobs
        'php artisan queue:restart',

        // Final optimization
        'php artisan optimize',
    ],

    /**
     * Timeout Setting
     * Define the timeout for each command in seconds.
     */
    'timeout' => 300,
];
