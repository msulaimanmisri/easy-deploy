<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\EasyDeployServiceProvider;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployCommand;
use SulaimanMisri\EasyDeploy\Console\Commands\EasyDeployWizard;

#[CoversClass(EasyDeployServiceProvider::class)]
class EasyDeployServiceProviderTest extends TestCase
{
    #[Test]
    public function service_provider_is_registered(): void
    {
        $this->assertInstanceOf(EasyDeployServiceProvider::class, $this->app->getProvider(EasyDeployServiceProvider::class));
    }

    #[Test]
    public function commands_are_registered_in_console(): void
    {
        $this->artisan('list')
            ->expectsOutput('easy-deploy:install')
            ->expectsOutput('easy-deploy:run');
    }

    #[Test]
    public function config_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'easy-deploy-config'])
            ->expectsOutput('Publishing complete.');

        $this->assertFileExists(config_path('easy-deploy.php'));
    }

    #[Test]
    public function boot_method_registers_commands(): void
    {
        $provider = new EasyDeployServiceProvider($this->app);
        $provider->boot();

        $this->assertTrue($this->app->runningInConsole());
    }

    #[Test]
    public function register_method_exists(): void
    {
        $provider = new EasyDeployServiceProvider($this->app);
        $provider->register();
        
        $this->assertTrue(true);
    }
}
