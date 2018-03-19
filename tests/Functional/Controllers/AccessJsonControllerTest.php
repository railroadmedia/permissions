<?php

namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\AccessFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\AccessService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AccessJsonControllerTest extends PermissionsTestCase
{

    /**
     * @var AccessFactory
     */
    protected $accessFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->accessFactory = $this->app->make(AccessFactory::class);
    }

    public function test_store()
    {
        $permission = [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'description' => $this->faker->text
        ];
        $results = $this->call('PUT', '/access', $permission);

        $this->assertEquals(200, $results->getStatusCode());
        $this->assertEquals(array_merge(
            [
                'id' => 1,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null,
                'brand' => ConfigService::$brand
            ]
            , $permission), $results->decodeResponseJson()['results']);
    }

    public function test_store_validation_errors()
    {
        $results = $this->call('PUT', '/access');

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
        $results = $this->call('PATCH', '/access/' . $randomId);

        $this->assertEquals(404, $results->getStatusCode());

        $this->assertEquals(
            [
                "title" => "Not found.",
                "detail" => "Update failed, access not found with id: " . $randomId,
            ]
            , $results->decodeResponseJson()['error']);
    }

    public function test_update()
    {
        $permission = $this->accessFactory->store();
        $updatedName = $this->faker->word;

        $results = $this->call('PATCH', '/access/' . $permission['id'],
            [
                'name' => $updatedName
            ]);

        $this->assertEquals(201, $results->getStatusCode());
        $this->assertEquals([
            'id' => $permission['id'],
            'name' => $updatedName,
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
        $results = $this->call('DELETE', '/access/' . $randomId);
        $this->assertEquals(404, $results->getStatusCode());

        $this->assertEquals(
            [
                "title" => "Not found.",
                "detail" => "Delete failed, access not found with id: " . $randomId,
            ]
            , $results->decodeResponseJson()['error']);
    }

    public function test_delete()
    {
        $permission = $this->accessFactory->store();
        $results = $this->call('DELETE', '/access/' . $permission['id']);

        $this->assertEquals(204, $results->getStatusCode());
    }
}
