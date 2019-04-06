<?php

namespace Laraning\Larapush;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laraning\Larapush\Commands\DeployCommand;
use Laraning\Larapush\Commands\InstallLocalCommand;
use Laraning\Larapush\Commands\InstallRemoteCommand;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

final class DeployerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfiguration();

        if (config('deployer.type') == 'remote' && class_exists('\Laravel\Passport\Passport')) {
            $this->loadRemoteRoutes();
        }

        $this->registerStorage();
    }

    private function registerStorage()
    {
        $this->app['config']->set('filesystems.disks', [
            'deployer' => [
                'driver' => 'local',
                'root' => app('config')->get('deployer.storage.path'),
            ],
        ]);
    }

    protected function publishConfiguration()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../configuration/deployer.php' => config_path('deployer.php'),
            ], 'deployer-configuration');
        }
    }

    protected function loadRemoteRoutes()
    {
        // Load Deployer routes using the api middleware.
        Route::as('deployer.')
             ->middleware('same-token', 'client')
             ->namespace('Laraning\Larapush\Http\Controllers')
             ->prefix(app('config')->get('deployer.remote.prefix'))
             ->group(__DIR__.'/../routes/api.php');

        // Load Laravel Passport routes.
        Passport::routes();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../configuration/deployer.php',
            'deployer'
        );

        $this->commands([
            InstallRemoteCommand::class,
            InstallLocalCommand::class,
            DeployCommand::class,
        ]);

        app('router')->aliasMiddleware(
            'client',
            CheckClientCredentials::class
        );

        app('router')->aliasMiddleware(
            'is-json',
            \Laraning\Larapush\Middleware\IsJson::class
        );

        app('router')->aliasMiddleware(
            'same-token',
            \Laraning\Larapush\Middleware\SameToken::class
        );
    }
}
