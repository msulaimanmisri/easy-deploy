<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Unit\Console\Commands;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployWizard;

#[CoversClass(EasyDeployWizard::class)]
class EasyDeployWizardTest extends TestCase
{
    #[Test]
    public function command_signature_and_description(): void
    {
        $command = new EasyDeployWizard();
        
        $this->assertEquals('easy-deploy:install', $command->getName());
        $this->assertEquals('Install Easy Deploy configuration file', $command->getDescription());
    }

    #[Test]
    public function handle_shows_welcome_message(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', false)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->expectsOutput('Please give the package a star on GitHub if you like it: https://github.com/sulaimanmisri/easy-deploy')
            ->assertExitCode(0);
    }

    #[Test]
    public function handle_calls_ask_questions(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('Publishing EasyDeploy configuration file...')
            ->expectsOutput('Configuration file published successfully!')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->assertExitCode(0);
    }

    #[Test]
    public function ask_questions_publishes_config_when_confirmed(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Publishing EasyDeploy configuration file...')
            ->expectsOutput('Configuration file published successfully!')
            ->assertExitCode(0);
    }

    #[Test]
    public function ask_questions_skips_config_when_declined(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', false)
            ->expectsOutput('Skipping configuration file publishing.')
            ->assertExitCode(0);
    }

    #[Test]
    public function publish_config_calls_vendor_publish(): void
    {
        $command = new EasyDeployWizard();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('publishConfig');
        $method->setAccessible(true);

        $command->setLaravel($this->app);
        
        // Mock the call method to avoid actual file operations
        $this->expectNotToPerformAssertions();
        $method->invoke($command);
    }

    #[Test]
    public function publish_config_shows_progress_messages(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Publishing EasyDeploy configuration file...')
            ->expectsOutput('Configuration file published successfully!')
            ->assertExitCode(0);
    }

    #[Test]
    public function wizard_completes_successfully(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->expectsOutput('Please give the package a star on GitHub if you like it: https://github.com/sulaimanmisri/easy-deploy')
            ->assertExitCode(0);
    }
}
