<?php

namespace Railroad\Permissions\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Railroad\Permissions\Services\ConfigService;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * The middlewares to be registered.
     *
     * @var array
     */

    protected $middlewares = [
        'permission' => \Railroad\Permissions\Middleware\PermissionsMiddleware::class,
    ];

    /**
     * Bootstrap the application Services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->setupConfig();

        $this->publishes(
            [
                __DIR__ . '/../../config/permissions.php' => config_path('permissions.php'),
            ]
        );

        // Append the country settings
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/permissions.php',
            'permissions'
        );

        if (ConfigService::$databaseMode == 'host') {
            $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        }

        // load package routes file
        $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');

        $this->registerMiddlewares();

        // configure resora
        config()->set('resora.default_connection_name', ConfigService::$databaseConnectionName);
        config()->set('resora.default_cache_driver', ConfigService::$cacheDriver);
    }

    private function setupConfig()
    {
        // caching
        ConfigService::$cacheDriver = config('permissions.cache_driver');
        ConfigService::$cacheTime = config('permissions.cache_duration');

        // database
        ConfigService::$databaseConnectionName = config('permissions.database_connection_name');
        ConfigService::$databaseMode = config('permissions.database_mode');

        // tables
        ConfigService::$tablePrefix = config('permissions.table_prefix');
        ConfigService::$tableUserAbilities = config('permissions.tables.user_abilities');
        ConfigService::$tableUserRoles = config('permissions.tables.user_roles');

        // role abilities
        ConfigService::$roleAbilities = config('permissions.role_abilities');
    }

    /**
     * Register the application Services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Register the middlewares automatically.
     *
     * @return void
     */

    protected function registerMiddlewares()
    {
        $router = $this->app['router'];

        if (method_exists($router, 'middleware')) {
            $registerMethod = 'middleware';
        } elseif (method_exists($router, 'aliasMiddleware')) {
            $registerMethod = 'aliasMiddleware';
        } else {
            return;
        }

        foreach ($this->middlewares as $key => $class) {
            $router->$registerMethod($key, $class);
        }
    }
}