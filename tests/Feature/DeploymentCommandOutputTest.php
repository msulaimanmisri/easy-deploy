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
class DeploymentCommandOutputTest extends TestCase
{
    #[Test]
    public function deployment_command_debug_output(): void
    {
        config(['easy-deploy.commands' => ['php --version']]);

        $this->artisan('easy-deploy:run')
            ->expectsOutputToContain('Starting deployment process')
            ->assertExitCode(0);
    }
}
