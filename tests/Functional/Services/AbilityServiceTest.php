<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\AbilityService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AbilityServiceTest extends PermissionsTestCase
{
    /**
     * @var AbilityService
     */
    protected $classBeingTested;

    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(AbilityService::class);
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
    }

    public function test_store_ability()
    {
        $name = $this->faker->word;
        $slug = $this->faker->slug;
        $type = $this->faker->randomElement([
            AbilityService::ROLE_TYPE,
            AbilityService::PERMISSION_TYPE
        ]);
        $description = $this->faker->text;

        $results = $this->classBeingTested->store(
            $name,
            $slug,
            $type,
            $description
        );

        $this->assertEquals([
            'id' => 1,
            'name' => $name,
            'slug' => $slug,
            'type' => $type,
            'description' => $description,
            'brand' => ConfigService::$brand,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableAbility,
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
        $permission = $this->permissionFactory->store();
        $results = $this->classBeingTested->getById($permission['id']);

        $this->assertEquals($permission, $results);
    }

    public function test_get_by_id_not_exist()
    {
        $results = $this->classBeingTested->getById(rand());

        $this->assertNull($results);
    }

    public function test_update()
    {
        $permission = $this->permissionFactory->store();
        $newName = $this->faker->word;

        $results = $this->classBeingTested->update($permission['id'], [
            'name' => $newName
        ]);
        $this->assertEquals([
            'id' => $permission['id'],
            'name' => $newName,
            'slug' => $permission['slug'],
            'type' => $permission['type'],
            'description' => $permission['description'],
            'brand' => ConfigService::$brand,
            'created_on' => $permission['created_on'],
            'updated_on' => Carbon::now()->toDateTimeString()
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableAbility,
            [
                'id' => $permission['id'],
                'name' => $newName,
                'slug' => $permission['slug'],
                'type' => $permission['type'],
                'description' => $permission['description'],
                'brand' => $permission['brand'],
                'created_on' => $permission['created_on'],
                'updated_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function test_update_not_existing_permission()
    {
        $results = $this->classBeingTested->update(rand(), []);

        $this->assertNull($results);
    }

    public function test_delete()
    {
        $permission = $this->permissionFactory->store();
        $results = $this->classBeingTested->delete($permission['id']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableAbility,
            [
                'id' => $permission['id'],
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'type' => $permission['type'],
                'description' => $permission['description'],
                'brand' => $permission['brand'],
                'created_on' => $permission['created_on'],
                'updated_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function test_delete_when_not_exist_permission()
    {
        $results = $this->classBeingTested->delete(rand());
        $this->assertNull($results);
    }
}
