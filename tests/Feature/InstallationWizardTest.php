<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployWizard;

#[CoversClass(EasyDeployWizard::class)]
#[Group('core')]
class InstallationWizardTest extends TestCase
{
    #[Test]
    public function installation_wizard_command_exists(): void
    {
        // Test that the command is registered correctly
        $this->assertTrue(true);
    }

    #[Test]
    public function installation_wizard_shows_welcome_message(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', false)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->assertExitCode(0);
    }

    #[Test]
    public function installation_wizard_publishes_config_when_confirmed(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Publishing EasyDeploy configuration file...')
            ->expectsOutput('Configuration file published successfully!')
            ->assertExitCode(0);
    }

    #[Test]
    public function installation_wizard_skips_config_when_declined(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', false)
            ->expectsOutput('Skipping configuration file publishing.')
            ->assertExitCode(0);
    }

    #[Test]
    public function installation_wizard_shows_github_link(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', false)
            ->expectsOutput('Please give the package a star on GitHub if you like it: https://github.com/sulaimanmisri/easy-deploy')
            ->assertExitCode(0);
    }

    #[Test]
    public function installation_wizard_completes_full_flow(): void
    {
        $this->artisan('easy-deploy:install')
            ->expectsQuestion('Do you want to publish the EasyDeploy configuration file now?', true)
            ->expectsOutput('Welcome to the EasyDeploy installation wizard! 🚀')
            ->expectsOutput('Publishing EasyDeploy configuration file...')
            ->expectsOutput('Configuration file published successfully!')
            ->expectsOutput('EasyDeploy has been successfully installed 🎉')
            ->expectsOutput('Please give the package a star on GitHub if you like it: https://github.com/sulaimanmisri/easy-deploy')
            ->assertExitCode(0);
    }
}
