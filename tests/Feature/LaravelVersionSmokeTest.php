<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\EasyDeployServiceProvider;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployCommand;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployWizard;

/**
 * Smoke test that proves the package loads and runs against the currently
 * installed Laravel version. It is executed for every leg of the CI matrix
 * (Laravel 12 + Laravel 13) so a regression on any supported version fails fast.
 */
#[CoversClass(EasyDeployServiceProvider::class)]
#[CoversClass(EasyDeployCommand::class)]
#[CoversClass(EasyDeployWizard::class)]
#[Group('core')]
class LaravelVersionSmokeTest extends TestCase
{
    #[Test]
    public function service_provider_is_registered(): void
    {
        $this->assertInstanceOf(
            EasyDeployServiceProvider::class,
            $this->app->getProvider(EasyDeployServiceProvider::class)
        );
    }

    #[Test]
    public function both_commands_are_registered_with_the_console(): void
    {
        $commands = array_keys($this->app->make(\Illuminate\Contracts\Console\Kernel::class)->all());

        $this->assertContains('easy-deploy:install', $commands);
        $this->assertContains('easy-deploy:run', $commands);
    }

    #[Test]
    public function installation_wizard_runs_end_to_end(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', false)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->assertExitCode(0);
    }

    #[Test]
    public function deployment_command_runs_a_safe_command(): void
    {
        config(['easy-deploy.commands' => ['php --version']]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
    }

    #[Test]
    public function default_config_is_valid_and_filterable(): void
    {
        $command = new EasyDeployCommand();
        $commands = $command->getCommandFromTheConfig();

        $this->assertNotEmpty($commands);
        $this->assertContains('php artisan migrate --force', $commands);

        // Invalid commands must never slip through the allow-list.
        config(['easy-deploy.commands' => ['npm install', 'rm -rf /']]);
        $this->assertEmpty($command->getCommandFromTheConfig());
    }
}
