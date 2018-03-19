<?php


namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\AccessFactory;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Factories\AccessHierarchyFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AccessHierarchyJsonControllerTest extends PermissionsTestCase
{
    /**
     * @var AccessFactory
     */
    protected $accessFactory;

    /**
     * @var AccessHierarchyFactory
     */
    protected $accessHierarchyFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->accessFactory = $this->app->make(AccessFactory::class);
        $this->accessHierarchyFactory = $this->app->make(AccessHierarchyFactory::class);
    }


    public function test_create_ability_hierarchy_validation()
    {
        $results = $this->call('PUT', 'access-hierarchy', []);

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "parent_id",
                "detail" => "The parent id field is required.",
            ],
            [
                "source" => "child_id",
                "detail" => "The child id field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_assign_permission_to_role()
    {
        $permission = $this->accessFactory->store();
        $role = $this->accessFactory->store();

        $results = $this->call('PUT', 'access-hierarchy', [
            'parent_id' => $permission['id'],
            'child_id' => $role['id']
        ]);

        $this->assertEquals(200, $results->getStatusCode());

        $this->assertEquals([
            'id' => 1,
            'parent_id' => $permission['id'],
            'child_id' => $role['id'],
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results->decodeResponseJson()['results']);

        $this->assertDatabaseHas(
            ConfigService::$tableAccessHierarchy,
            [
                'id' => 1,
                'parent_id' => $permission['id'],
                'child_id' => $role['id']
            ]
        );
    }

    public function test_revoke_role_permission_not_exist()
    {
        $results = $this->call('DELETE', 'access-hierarchy', [
            'parent_id' => rand()
        ]);

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "parent_id",
                "detail" => "The selected parent id is invalid.",
            ],
            [
                "source" => "child_id",
                "detail" => "The child id field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_revoke_role_permission()
    {
        $permission = $this->accessFactory->store();
        $role = $this->accessFactory->store();
        $abilityHierarchy = $this->accessHierarchyFactory->store($permission['id'], $role['id']);

        $results = $this->call('DELETE', 'access-hierarchy', [
            'parent_id' => $permission['id'],
            'child_id' => $role['id']
        ]);

        $this->assertEquals(204, $results->getStatusCode());

        $this->assertDatabaseMissing(
            ConfigService::$tableAccessHierarchy,
            [
                'id' => $abilityHierarchy['id'],
                'parent_id' => $permission['id'],
                'child_id' => $role['id']
            ]
        );
    }
}
