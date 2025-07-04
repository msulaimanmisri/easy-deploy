<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use SulaimanMisri\EasyDeploy\EasyDeployServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EasyDeployServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('easy-deploy.commands', [
            'composer install --no-dev --optimize-autoloader',
            'php artisan migrate --force',
            'php artisan clear-compiled',
            'php artisan optimize:clear',
            'php artisan storage:link',
            'chmod -R 775 storage bootstrap/cache',
            'php artisan queue:restart',
            'php artisan optimize',
        ]);

        $app['config']->set('easy-deploy.timeout', 300);
    }
}
