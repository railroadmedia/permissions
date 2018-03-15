<?php


namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\RoleService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class RoleServiceTest extends PermissionsTestCase
{
    /**
     * @var RoleService
     */
    protected $classBeingTested;

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(RoleService::class);
        $this->roleFactory = $this->app->make(RoleFactory::class);
    }

    public function test_store()
    {
        $name = $this->faker->word;
        $slug = $this->faker->slug;
        $description = $this->faker->text;
        $results = $this->classBeingTested->store(
            $name,
            $slug,
            $description
        );

        $this->assertEquals([
            'id' => 1,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'brand' => ConfigService::$brand,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableRole,
            [
                'id' => 1,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'brand' => ConfigService::$brand,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null
            ]
        );
    }

    public function test_get_by_Id()
    {
        $role = $this->roleFactory->store();
        $results = $this->classBeingTested->getById($role['id']);

        $this->assertEquals($role, $results);
    }

    public function test_get_by_id_not_exist()
    {
        $results = $this->classBeingTested->getById(rand());

        $this->assertNull($results);
    }

    public function test_update()
    {
        $role = $this->roleFactory->store();

        $newName = $this->faker->word;

        $results = $this->classBeingTested->update($role['id'], [
            'name' => $newName
        ]);

        $this->assertEquals([
            'id' => $role['id'],
            'name' => $newName,
            'slug' => $role['slug'],
            'description' => $role['description'],
            'brand' => ConfigService::$brand,
            'created_on' => $role['created_on'],
            'updated_on' => Carbon::now()->toDateTimeString()
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableRole,
            [
                'id' => $role['id'],
                'name' => $newName,
                'slug' => $role['slug'],
                'description' => $role['description'],
                'brand' => $role['brand'],
                'created_on' => $role['created_on'],
                'updated_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function test_update_not_existing_role()
    {
        $results = $this->classBeingTested->update(rand(), []);

        $this->assertNull($results);
    }

    public function test_delete()
    {
        $role = $this->roleFactory->store();
        $results = $this->classBeingTested->delete($role['id']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableRole,
            [
                'id' => $role['id'],
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
                'brand' => $role['brand'],
                'created_on' => $role['created_on'],
                'updated_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function test_delete_when_not_exist_role()
    {
        $results = $this->classBeingTested->delete(rand());
        $this->assertNull($results);
    }
}
