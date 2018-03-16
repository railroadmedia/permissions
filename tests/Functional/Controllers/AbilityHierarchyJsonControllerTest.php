<?php


namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Factories\AbilityHierarchyFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AbilityHierarchyJsonControllerTest extends PermissionsTestCase
{
    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    /**
     * @var AbilityHierarchyFactory
     */
    protected $abilityHierarchyFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
        $this->roleFactory = $this->app->make(RoleFactory::class);
        $this->abilityHierarchyFactory = $this->app->make(AbilityHierarchyFactory::class);
    }


    public function test_create_ability_hierarchy_validation()
    {
        $results = $this->call('PUT', 'ability-hierarchy', []);

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
        $permission = $this->permissionFactory->store();
        $role = $this->roleFactory->store();

        $results = $this->call('PUT', 'ability-hierarchy', [
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
            ConfigService::$tableAbilityHierarchy,
            [
                'id' => 1,
                'parent_id' => $permission['id'],
                'child_id' => $role['id']
            ]
        );
    }

    public function test_revoke_role_permission_not_exist()
    {
        $results = $this->call('DELETE', 'ability-hierarchy', [
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
        $permission = $this->permissionFactory->store();
        $role = $this->roleFactory->store();
        $abilityHierarchy = $this->abilityHierarchyFactory->store($permission['id'], $role['id']);

        $results = $this->call('DELETE', 'ability-hierarchy', [
            'parent_id' => $permission['id'],
            'child_id' => $role['id']
        ]);

        $this->assertEquals(204, $results->getStatusCode());

        $this->assertDatabaseMissing(
            ConfigService::$tableAbilityHierarchy,
            [
                'id' => $abilityHierarchy['id'],
                'parent_id' => $permission['id'],
                'child_id' => $role['id']
            ]
        );
    }
}
