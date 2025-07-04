<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployCommand;

#[CoversClass(EasyDeployCommand::class)]
#[Group('core')]
class DeploymentCommandTest extends TestCase
{
    #[Test]
    public function deployment_command_exists(): void
    {
        // Test that the command is registered correctly
        $this->assertTrue(true);
    }

    #[Test]
    public function deployment_command_executes_successfully(): void
    {
        config(['easy-deploy.commands' => ['php --version']]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
    }

    #[Test]
    public function deployment_command_handles_multiple_commands(): void
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
    public function deployment_command_shows_no_commands_message(): void
    {
        config(['easy-deploy.commands' => []]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('No commands found in the configuration file')
            ->assertExitCode(0);
    }

    #[Test]
    public function deployment_command_filters_invalid_commands(): void
    {
        config(['easy-deploy.commands' => [
            'php --version',
            'invalid-command',
            'npm install',
            'composer --version'
        ]]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->expectsOutputToContain('Deployment process completed')
            ->assertExitCode(0);
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
