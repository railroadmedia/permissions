<?php

namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\AbilityService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AbilityJsonControllerTest extends PermissionsTestCase
{

    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
    }

    public function test_store_permission()
    {
        $permission = [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'description' => $this->faker->text
        ];
        $results = $this->call('PUT', '/permission', $permission);

        $this->assertEquals(200, $results->getStatusCode());
        $this->assertEquals(array_merge(
            [
                'id' => 1,
                'type' => AbilityService::PERMISSION_TYPE,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null,
                'brand' => ConfigService::$brand
            ]
            , $permission), $results->decodeResponseJson()['results']);
    }

    public function test_store()
    {
        $role = [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'description' => $this->faker->text
        ];
        $results = $this->call('PUT', '/role', $role);

        $this->assertEquals(200, $results->getStatusCode());
        $this->assertEquals(array_merge(
            [
                'id' => 1,
                'type' => AbilityService::ROLE_TYPE,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null,
                'brand' => ConfigService::$brand
            ]
            , $role), $results->decodeResponseJson()['results']);
    }


    public function test_store_validation_errors()
    {
        $results = $this->call('PUT', '/permission');

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "name",
                "detail" => "The name field is required.",
            ],
            [
                "source" => "slug",
                "detail" => "The slug field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }


    public function test_update_not_existing_permission()
    {
        $randomId = rand();
        $results = $this->call('PATCH', '/ability/' . $randomId);

        $this->assertEquals(404, $results->getStatusCode());

        $this->assertEquals(
            [
                "title" => "Not found.",
                "detail" => "Update failed, ability not found with id: " . $randomId,
            ]
            , $results->decodeResponseJson()['error']);
    }

    public function test_update()
    {
        $permission = $this->permissionFactory->store();
        $updatedName = $this->faker->word;

        $results = $this->call('PATCH', '/ability/' . $permission['id'],
            [
                'name' => $updatedName
            ]);

        $this->assertEquals(201, $results->getStatusCode());
        $this->assertEquals([
            'id' => $permission['id'],
            'name' => $updatedName,
            'type' => AbilityService::PERMISSION_TYPE,
            'description' => $permission['description'],
            'slug' => $permission['slug'],
            'brand' => $permission['brand'],
            'created_on' => $permission['created_on'],
            'updated_on' => Carbon::now()->toDateTimeString()
        ], $results->decodeResponseJson()['results']);
    }

    public function test_delete_not_existing_permission()
    {
        $randomId = rand();
        $results = $this->call('DELETE', '/ability/' . $randomId);
        $this->assertEquals(404, $results->getStatusCode());

        $this->assertEquals(
            [
                "title" => "Not found.",
                "detail" => "Delete failed, ability not found with id: " . $randomId,
            ]
            , $results->decodeResponseJson()['error']);
    }

    public function test_delete()
    {
        $permission = $this->permissionFactory->store();
        $results = $this->call('DELETE', '/ability/' . $permission['id']);

        $this->assertEquals(204, $results->getStatusCode());
    }
}
