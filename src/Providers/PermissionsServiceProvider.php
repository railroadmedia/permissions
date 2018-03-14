<?php

namespace Railroad\Permissions\Providers;

use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Railroad\Permissions\Services\ConfigService;


class PermissionsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application Services.
     *
     * @return void
     */
    public function boot()
    {
        $this->listen = [
            StatementPrepared::class => [
                function (StatementPrepared $event) {

                    // we only want to use assoc fetching for this packages database calls
                    // so we need to use a separate 'mask' connection

                    if ($event->connection->getName() ==
                        ConfigService::$connectionMaskPrefix . ConfigService::$databaseConnectionName) {
                        $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
                    }
                }
            ],
        ];

        parent::boot();

        $this->setupConfig();

        $this->publishes(
            [
                __DIR__ . '/../../config/permissions.php' => config_path('permissions.php'),
            ]
        );

        if (ConfigService::$dataMode == 'host') {
            $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        }

        //load package routes file
        $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');

    }

    private function setupConfig()
    {
        // caching
        ConfigService::$cacheTime = config('permissions.cache_duration');

        // database
        ConfigService::$databaseConnectionName = config('permissions.database_connection_name');
        ConfigService::$connectionMaskPrefix = config('permissions.connection_mask_prefix');
        ConfigService::$dataMode = config('permissions.data_mode');

        // tables
        ConfigService::$tablePrefix = config('permissions.table_prefix');

        ConfigService::$tablePermissions = ConfigService::$tablePrefix . 'permissions';
        ConfigService::$tableUserPermission = ConfigService::$tablePrefix . 'user_permission';

        ConfigService::$brand = config('permissions.brand');
    }

    /**
     * Register the application Services.
     *
     * @return void
     */
    public function register()
    {

    }
}