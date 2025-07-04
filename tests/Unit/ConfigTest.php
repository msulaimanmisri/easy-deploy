<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use SulaimanMisri\EasyDeploy\Tests\TestCase;
use SulaimanMisri\EasyDeploy\EasyDeployServiceProvider;

#[CoversClass(EasyDeployServiceProvider::class)]
class ConfigTest extends TestCase
{
    #[Test]
    public function config_has_required_keys(): void
    {
        $config = include __DIR__ . '/../../src/config/easy-deploy.php';

        $this->assertArrayHasKey('commands', $config);
        $this->assertArrayHasKey('timeout', $config);
    }

    #[Test]
    public function config_commands_are_array(): void
    {
        $config = include __DIR__ . '/../../src/config/easy-deploy.php';

        $this->assertIsArray($config['commands']);
        $this->assertNotEmpty($config['commands']);
    }

    #[Test]
    public function config_timeout_is_integer(): void
    {
        $config = include __DIR__ . '/../../src/config/easy-deploy.php';

        $this->assertIsInt($config['timeout']);
        $this->assertGreaterThan(0, $config['timeout']);
    }

    #[Test]
    public function config_commands_contain_expected_commands(): void
    {
        $config = include __DIR__ . '/../../src/config/easy-deploy.php';

        $expectedCommands = [
            'composer install --no-dev --optimize-autoloader',
            'php artisan migrate --force',
            'php artisan clear-compiled',
            'php artisan optimize:clear',
            'php artisan storage:link',
            'chmod -R 775 storage bootstrap/cache',
            'php artisan queue:restart',
            'php artisan optimize',
        ];

        foreach ($expectedCommands as $command) {
            $this->assertContains($command, $config['commands']);
        }
    }

    #[Test]
    public function config_timeout_default_value(): void
    {
        $config = include __DIR__ . '/../../src/config/easy-deploy.php';

        $this->assertEquals(300, $config['timeout']);
    }
}
