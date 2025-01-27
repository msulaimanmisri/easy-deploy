<?php

declare(strict_types=1);

namespace SulaimanMisri\EasyDeploy\Console\Commands;

use Illuminate\Console\Command;

class EasyDeployWizard extends Command
{
    protected $signature = 'easy-deploy:install';
    protected $description = 'Install Easy Deploy configuration file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Welcome to the EasyDeploy installation wizard! 🚀');

        $this->askQuestions();

        $this->info('EasyDeploy has been successfully installed 🎉');
        $this->info('Please give the package a star on GitHub if you like it: https://github.com/sulaimanmisri/easy-deploy');
    }

    /**
     * Series of questions
     */
    protected function askQuestions(): void
    {
        // Question 1: Publish the configuration file
        if ($this->confirm('Do you want to publish the EasyDeploy configuration file now?', true)) {
            $this->publishConfig();
        } else {
            $this->line('Skipping configuration file publishing.');
        }
    }

    /**
     * Publish the EasyDeploy configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('Publishing EasyDeploy configuration file...');

        $this->call('vendor:publish', [
            '--tag' => 'easy-deploy-config',
        ]);

        $this->info('Configuration file published successfully!');
    }
}
