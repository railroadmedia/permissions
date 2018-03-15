<?php

namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class RoleJsonControllerTest extends PermissionsTestCase
{

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->roleFactory = $this->app->make(RoleFactory::class);
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
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null,
                'brand' => ConfigService::$brand
            ]
            , $role), $results->decodeResponseJson()['results']);
    }

    public function test_store_validation_errors()
    {
        $results = $this->call('PUT', '/role');

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


    public function test_update_not_existing_role()
    {
        $randomId = rand();
        $results = $this->call('PATCH', '/role/' . $randomId);

        $this->assertEquals(404, $results->getStatusCode());

        $this->assertEquals(
            [
                "title" => "Not found.",
                "detail" => "Update failed, role not found with id: " . $randomId,
            ]
            , $results->decodeResponseJson()['error']);
    }

    public function test_update()
    {
        $role = $this->roleFactory->store();
        $updatedName = $this->faker->word;

        $results = $this->call('PATCH', '/role/' . $role['id'],
            [
                'name' => $updatedName
            ]);

        $this->assertEquals(201, $results->getStatusCode());
        $this->assertEquals([
            'id' => $role['id'],
            'name' => $updatedName,
            'description' => $role['description'],
            'slug' => $role['slug'],
            'brand' => $role['brand'],
            'created_on' => $role['created_on'],
            'updated_on' => Carbon::now()->toDateTimeString()
        ], $results->decodeResponseJson()['results']);
    }

    public function test_delete_not_existing_role()
    {
        $randomId = rand();
        $results = $this->call('DELETE', 'role/' . $randomId);
        $this->assertEquals(404, $results->getStatusCode());

        $this->assertEquals(
            [
                "title" => "Not found.",
                "detail" => "Delete failed, role not found with id: " . $randomId,
            ]
            , $results->decodeResponseJson()['error']);
    }

    public function test_delete()
    {
        $role = $this->roleFactory->store();
        $results = $this->call('DELETE', '/role/' . $role['id']);

        $this->assertEquals(204, $results->getStatusCode());
    }
}
