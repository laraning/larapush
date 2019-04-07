<?php

namespace Laraning\Larapush\Commands;

use Laraning\Larapush\Support\Local;
use Laraning\Larapush\Concerns\SimplifiesConsoleOutput;
use Laraning\Larapush\Abstracts\InstallerBootstrap;

final class PushCommand extends InstallerBootstrap
{
    use SimplifiesConsoleOutput;

    protected $signature = 'push';

    protected $description = 'Pushes your codebase content to your remote environment';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        parent::handle();

        $this->steps = 8;

        $bar = $this->output->createProgressBar($this->steps);
        $bar->start();

        $this->runPreChecks();
        $bar->advance();

        $this->pingRemote();
        $bar->advance();

        $this->askRemoteForPreChecks();
        $bar->advance();

        // The repository code generated in this moment is the PK for all the next transactions.
        $this->transaction = generate_transaction_code();

        $this->createLocalRepository();
        $bar->advance();

        $this->uploadCodebase();
        $bar->advance();

        $this->runPreScripts();
        $bar->advance();

        $this->deploy();
        $bar->advance();

        $this->runPostScripts();
        $bar->finish();

        $this->bulkInfo(2, '*** All good! Codebase pushed to your remote server! ***', 1);
    }

    protected function createLocalRepository()
    {
        $this->bulkInfo(2, "Creating local environment codebase repository ({$this->transaction})...", 1);

        larapush_rescue(function () {
            Local::createRepository($this->transaction);
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function runPostScripts()
    {
        $this->bulkInfo(2, 'Running your post-scripts after unpacking your codebase (if they exist)...', 1);

        larapush_rescue(function () {
            Local::getAccessToken()
                 ->runPostScripts($this->transaction);
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function deploy()
    {
        $this->bulkInfo(2, 'Unpacking your codebase on your remote server...', 1);

        larapush_rescue(function () {
            Local::getAccessToken()
                 ->deploy($this->transaction);
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function runPreScripts()
    {
        $this->bulkInfo(2, 'Running your pre-scripts before unpacking your codebase (if they exist)...', 1);

        larapush_rescue(function () {
            Local::getAccessToken()
                 ->runPreScripts($this->transaction);
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function uploadCodebase()
    {
        $this->bulkInfo(2, 'Uploading codebase to your remote environment...', 1);

        larapush_rescue(function () {
            Local::getAccessToken()
                 ->uploadCodebase($this->transaction);
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function askRemoteForPreChecks()
    {
        $this->bulkInfo(2, 'Asking remote server to make its pre-checks...', 1);

        larapush_rescue(function () {
            Local::getAccessToken()
                 ->askRemoteForPreChecks();
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function pingRemote()
    {
        $this->bulkInfo(2, 'Checking OAuth & remote server connectivity...', 1);

        larapush_rescue(function () {
            Local::getAccessToken()
                 ->ping();
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function runPreChecks()
    {
        $this->bulkInfo(2, 'Checking your local storage availability/permissions...', 1);
        larapush_rescue(function () {
            Local::preChecks();
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }
}
