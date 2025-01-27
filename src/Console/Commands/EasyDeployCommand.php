<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EasyDeployCommand extends Command
{
    protected $signature = 'easy-deploy:run';
    protected $description = 'Run the deployment commands defined in the configuration';

    /**
     * Execute the console command
     */
    public function handle(): void
    {
        $this->info('Starting deployment process...');
        $commands = $this->getCommandFromTheConfig();

        if (empty($commands)) {
            $this->error('No commands found in the configuration file.');
            return;
        }

        $progressBar = $this->output->createProgressBar(count($commands));
        $progressBar->start();

        foreach ($commands as $command) {
            $this->info("\nRunning: {$command}");
            $this->runProcess($command);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nDeployment process completed!");
    }

    /**
     * Get the commands from the config file.
     */
    public function getCommandFromTheConfig(): array
    {
        $commands = config('easy-deploy.commands', []);

        return array_filter($commands, function ($command) {
            return str_starts_with($command, 'composer') || str_starts_with($command, 'php artisan');
        });
    }

    /**
     * Run the process
     */
    protected function runProcess($command) : void
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(config('easy-deploy.timeout', 300));

        try {
            $process->run();
        } catch (ProcessFailedException $e) {
            $this->error("Command failed: {$command}");
            $this->error($e->getMessage());
            logger()->error("Command failed: {$command}", ['error' => $e->getMessage()]);
            return;
        }

        $this->info($process->getOutput());
        logger()->info("Command executed: {$command}", ['output' => $process->getOutput()]);
    }
}
