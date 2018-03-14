<?php

namespace Railroad\Permissions\Tests;

use Faker\Generator;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Railroad\Permissions\Providers\PermissionsServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Router;
use Railroad\Permissions\Repositories\RepositoryBase;
use Railroad\Railcontent\Tests\Resources\Models\User;


class PermissionsTestCase extends BaseTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var AuthManager
     */
    protected $authManager;

    protected function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', []);
        $this->artisan('cache:clear', []);

        $this->faker = $this->app->make(Generator::class);
        $this->databaseManager = $this->app->make(DatabaseManager::class);
        $this->authManager = $this->app->make(AuthManager::class);
        $this->router = $this->app->make(Router::class);

        RepositoryBase::$connectionMask = null;

        Carbon::setTestNow(Carbon::now());
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // setup package config for testing
        $defaultConfig = require(__DIR__ . '/../config/permissions.php');

        $app['config']->set('permissions.database_connection_name', 'testbench');
        $app['config']->set('permissions.cache_duration', 60);
        $app['config']->set('permissions.table_prefix', $defaultConfig['table_prefix']);
        $app['config']->set('permissions.data_mode', $defaultConfig['data_mode']);
        $app['config']->set('permissions.brand', $defaultConfig['brand']);

        // setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.mysql',
            [
                'driver' => 'mysql',
                'host' => 'mysql',
                'port' => env('MYSQL_PORT', '3306'),
                'database' => env('MYSQL_DB','permissions'),
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix' => '',
                'options' => [
                    \PDO::ATTR_PERSISTENT => true,
                ]
            ]
        );

        $app['config']->set(
            'database.connections.testbench',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        // allows access to built in user auth
        $app['config']->set('auth.providers.users.model', User::class);

        // allows access to built in user auth
        $app['config']->set('auth.providers.users.model', User::class);


        if (!$app['db']->connection()->getSchemaBuilder()->hasTable('users')) {

            $app['db']->connection()->getSchemaBuilder()->create(
                'users',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('email');
                }
            );
        }
        // register provider
        $app->register(PermissionsServiceProvider::class);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}