<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Factories\AbilityHierarchyFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\AbilityHierarchyService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AbilityHierarchyServiceTest extends PermissionsTestCase
{
    /**
     * @var AbilityHierarchyService
     */
    protected $classBeingTested;

    /**
     * @var AbilityHierarchyFactory
     */
    protected $abilityHierarchyFactory;

    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(AbilityHierarchyService::class);
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
        $this->abilityHierarchyFactory = $this->app->make(AbilityHierarchyFactory::class);
        $this->roleFactory = $this->app->make(RoleFactory::class);
    }

    public function test_assign_permission_to_role()
    {
        $permissionId = rand();
        $roleId = rand();

        $results = $this->classBeingTested->saveAbilityHierarchy($permissionId, $roleId);

        $this->assertEquals([
            'id' => 1,
            'parent_id' => $permissionId,
            'child_id' => $roleId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableAbilityHierarchy,
            [
                'id' => 1,
                'parent_id' => $permissionId,
                'child_id' => $roleId,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null
            ]
        );
    }

    public function test_revoke_role_permission()
    {
        $permission = $this->permissionFactory->store();
        $role = $this->roleFactory->store();

        $rolePermission = $this->abilityHierarchyFactory->store($permission['id'], $role['id']);

        $results = $this->classBeingTested->deleteAbilityHierarchy($permission['id'], $role['id']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableAbilityHierarchy,
            [
                'id' => $rolePermission['id'],
                'child_id' => $role['id'],
                'parent_id' => $permission['id']
            ]
        );
    }

    public function test_revoke_role_pemission_when_not_exist()
    {
        $results = $this->classBeingTested->deleteAbilityHierarchy(rand(), rand());

        $this->assertNull($results);
    }

    public function test_role_has_permission_when_permission_exist()
    {
        $permission = $this->permissionFactory->store();
        $role = $this->roleFactory->store();
        $this->abilityHierarchyFactory->store($permission['id'], $role['id']);

        $results = $this->classBeingTested->abilityHasChild($permission['id'], $role['id']);

        $this->assertTrue($results);
    }

    public function test_role_has_permission_when_permission_not_exist()
    {
        $results = $this->classBeingTested->abilityHasChild(rand(), rand());

        $this->assertFalse($results);
    }
}
