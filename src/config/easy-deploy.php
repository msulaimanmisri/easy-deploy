<?php

declare(strict_types=1);

return [

    /**
     * Deployment Commands
     * Define the commands to be executed during deployment.
     * Add or remove commands as needed.
     */
    'commands' => [
        'composer install',
        'composer dump-autoload -o',
        'php artisan migrate --force',

        'php artisan clear-compiled',
        'php artisan optimize:clear',
        'php artisan optimize',
        
        'php artisan config:cache',
        'php artisan queue:restart',
    ],

    /**
     * Timeout Setting
     * Define the timeout for each command in seconds.
     */
    'timeout' => 300,
];
