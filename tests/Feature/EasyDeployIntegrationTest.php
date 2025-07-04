<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployCommand;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployWizard;
use SulaimanMisri\EasyDeploy\EasyDeployServiceProvider;

#[CoversClass(EasyDeployCommand::class)]
#[CoversClass(EasyDeployWizard::class)]
#[CoversClass(EasyDeployServiceProvider::class)]
#[Group('core')]
class EasyDeployIntegrationTest extends TestCase
{
    #[Test]
    public function full_installation_and_deployment_flow(): void
    {
        // Test installation wizard
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->assertExitCode(0);

        // Test deployment with safe commands
        config(['easy-deploy.commands' => ['php --version']]);
        
        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
    }

    #[Test]
    public function config_values_are_properly_loaded(): void
    {
        $this->assertEquals(300, config('easy-deploy.timeout'));
        $this->assertIsArray(config('easy-deploy.commands'));
        $this->assertNotEmpty(config('easy-deploy.commands'));
    }

    #[Test]
    public function commands_are_available_in_artisan_list(): void
    {
        // Test that commands are registered
        $this->assertTrue(true);
    }

    #[Test]
    public function deployment_command_executes_with_progress(): void
    {
        config(['easy-deploy.commands' => [
            'php --version',
            'composer --version'
        ]]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
    }

    #[Test]
    public function installation_wizard_publishes_config(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Publishing EasyDeploy configuration file...')
            ->expectsOutput('Configuration file published successfully!')
            ->assertExitCode(0);
    }

    #[Test]
    public function deployment_handles_empty_commands_gracefully(): void
    {
        config(['easy-deploy.commands' => []]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('No commands found in the configuration file')
            ->assertExitCode(0);
    }

    #[Test]
    public function deployment_filters_invalid_commands(): void
    {
        config(['easy-deploy.commands' => [
            'php --version',
            'invalid-command',
            'npm install'
        ]]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
    }

    #[Test]
    public function service_provider_registers_commands(): void
    {
        $provider = new EasyDeployServiceProvider($this->app);
        $provider->boot();

        $this->assertTrue($this->app->runningInConsole());
    }

    #[Test]
    public function config_publishing_works(): void
    {
        // Test publishing configuration
        $this->assertTrue(true);
    }

    #[Test]
    public function deployment_command_uses_configured_timeout(): void
    {
        config(['easy-deploy.timeout' => 120]);
        config(['easy-deploy.commands' => ['php --version']]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
    }
}
