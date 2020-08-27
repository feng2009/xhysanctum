<?php

namespace Xiaohuyun\xhysanctum;

use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Xiaohuyun\xhysanctum\Http\Controllers\CsrfCookieController;
use Xiaohuyun\xhysanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class SanctumServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'auth.guards.xhysanctum' => array_merge([
                'driver' => 'xhysanctum',
                'provider' => null,
            ], config('auth.guards.xhysanctum', [])),
        ]);

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/xhysanctum.php', 'xhysanctum');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'xhysanctum-migrations');

            $this->publishes([
                __DIR__.'/../config/xhysanctum.php' => config_path('xhysanctum.php'),
            ], 'xhysanctum-config');
        }

        $this->defineRoutes();
        $this->configureGuard();
        $this->configureMiddleware();
    }

    /**
     * Register xhysanctum's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Sanctum::shouldRunMigrations()) {
            return $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Define the Sanctum routes.
     *
     * @return void
     */
    protected function defineRoutes()
    {
        if ($this->app->routesAreCached() || config('xhysanctum.routes') === false) {
            return;
        }

        Route::group(['prefix' => config('xhysanctum.prefix', 'xhysanctum')], function () {

            Route::get(
                '/csrf-cookie',
                CsrfCookieController::class.'@show'
            )->middleware('web');
           
        });
    }

    /**
     * Configure the xhysanctum authentication guard.
     *
     * @return void
     */
    protected function configureGuard()
    {
         
        Auth::resolved(function ($auth) {
            $auth->extend('xhysanctum', function ($app, $name, array $config) use ($auth) {

                return tap($this->createGuard($auth, $config), function ($guard) {

                    $this->app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Register the guard.
     *
     * @param \Illuminate\Contracts\Auth\Factory  $auth
     * @param array $config
     * @return RequestGuard
     */
    protected function createGuard($auth, $config)
    {

        return new RequestGuard(
            new Guard($auth, config('xhysanctum.expiration'), $config['provider']),
            $this->app['request'],
            $auth->createUserProvider()
        );
    }

    /**
     * Configure the xhysanctum middleware and priority.
     *
     * @return void
     */
    protected function configureMiddleware()
    {

        $kernel = $this->app->make(Kernel::class);

        $kernel->prependToMiddlewarePriority(EnsureFrontendRequestsAreStateful::class);
    }
}
