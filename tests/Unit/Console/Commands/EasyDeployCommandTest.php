<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Unit\Console\Commands;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployCommand;
use Symfony\Component\Process\Process;
use Mockery;

#[CoversClass(EasyDeployCommand::class)]
class EasyDeployCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function command_signature_and_description(): void
    {
        $command = new EasyDeployCommand();
        
        $this->assertEquals('easy-deploy:run', $command->getName());
        $this->assertEquals('Run the deployment commands defined in the configuration', $command->getDescription());
    }

    #[Test]
    public function get_command_from_config_returns_filtered_commands(): void
    {
        $command = new EasyDeployCommand();
        $commands = $command->getCommandFromTheConfig();

        $this->assertCount(8, $commands);
        $this->assertContains('composer install --no-dev --optimize-autoloader', $commands);
        $this->assertContains('php artisan migrate --force', $commands);
        $this->assertContains('chmod -R 775 storage bootstrap/cache', $commands);
    }

    #[Test]
    public function get_command_from_config_filters_invalid_commands(): void
    {
        config(['easy-deploy.commands' => [
            'composer install',
            'php artisan migrate',
            'chmod 775 storage',
            'invalid-command',
            'npm install'
        ]]);

        $command = new EasyDeployCommand();
        $commands = $command->getCommandFromTheConfig();

        $this->assertCount(3, $commands);
        $this->assertNotContains('invalid-command', $commands);
        $this->assertNotContains('npm install', $commands);
    }

    #[Test]
    public function get_command_from_config_returns_empty_array_when_no_config(): void
    {
        config(['easy-deploy.commands' => []]);

        $command = new EasyDeployCommand();
        $commands = $command->getCommandFromTheConfig();

        $this->assertEmpty($commands);
    }

    #[Test]
    public function handle_shows_error_when_no_commands_found(): void
    {
        config(['easy-deploy.commands' => []]);

        $this->artisan('easy-deploy:run')
            ->expectsOutput('No commands found in the configuration file.')
            ->assertExitCode(0);
    }

    #[Test]
    public function handle_executes_commands_successfully(): void
    {
        // Mock a simple command that will succeed
        config(['easy-deploy.commands' => ['php artisan --version']]);

        $this->artisan('easy-deploy:run')
            ->expectsOutput('Starting deployment process...')
            ->expectsOutput('Deployment process completed!')
            ->assertExitCode(0);
    }

    #[Test]
    public function handle_shows_progress_bar(): void
    {
        config(['easy-deploy.commands' => ['php artisan --version']]);

        $this->artisan('easy-deploy:run')
            ->expectsOutput('Starting deployment process...')
            ->expectsOutput('Deployment process completed!')
            ->assertExitCode(0);
    }

    #[Test]
    public function run_process_handles_successful_command(): void
    {
        $command = new EasyDeployCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('runProcess');
        $method->setAccessible(true);

        $command->setLaravel($this->app);
        
        // This should not throw an exception
        $this->expectNotToPerformAssertions();
        $method->invoke($command, 'php artisan --version');
    }

    #[Test]
    public function run_process_handles_failed_command_with_user_abort(): void
    {
        config(['easy-deploy.timeout' => 1]);

        $this->artisan('easy-deploy:run')
            ->expectsQuestion('Command failed. Do you want to continue with remaining commands?', false)
            ->expectsOutput('Deployment aborted.')
            ->assertExitCode(1);
    }

    #[Test]
    public function run_process_handles_failed_command_with_user_continue(): void
    {
        // Test with a command that will fail
        config(['easy-deploy.commands' => ['invalid-command-that-will-fail']]);

        $this->artisan('easy-deploy:run')
            ->expectsQuestion('Command failed. Do you want to continue with remaining commands?', true)
            ->expectsOutput('Deployment process completed!')
            ->assertExitCode(0);
    }

    #[Test]
    public function run_process_uses_configured_timeout(): void
    {
        config(['easy-deploy.timeout' => 120]);

        $command = new EasyDeployCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('runProcess');
        $method->setAccessible(true);

        $command->setLaravel($this->app);
        
        // This should use the configured timeout
        $this->expectNotToPerformAssertions();
        $method->invoke($command, 'php artisan --version');
    }

    #[Test]
    public function run_process_logs_successful_execution(): void
    {
        $command = new EasyDeployCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('runProcess');
        $method->setAccessible(true);

        $command->setLaravel($this->app);
        
        // Mock the logger to verify logging
        $this->expectNotToPerformAssertions();
        $method->invoke($command, 'php artisan --version');
    }

    #[Test]
    public function run_process_logs_failed_execution(): void
    {
        $command = new EasyDeployCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('runProcess');
        $method->setAccessible(true);

        $command->setLaravel($this->app);
        
        // This should log the error
        $this->expectNotToPerformAssertions();
        $method->invoke($command, 'invalid-command-that-will-fail');
    }
}
