<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy;

use Illuminate\Support\ServiceProvider;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployCommand;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployWizard;

class EasyDeployServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider
     */
    public function boot(): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/config/easy-deploy.php' => config_path('easy-deploy.php'),
        ], 'easy-deploy-config');

        // Register the command
        if ($this->app->runningInConsole()) {
            $this->commands([
                EasyDeployWizard::class,
                EasyDeployCommand::class
            ]);
        }
    }

    /**
     * Register the service provider
     */
    public function register(): void
    {
        //
    }
}
