<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\AccessFactory;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Factories\AccessHierarchyFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\AccessHierarchyService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class AccessHierarchyServiceTest extends PermissionsTestCase
{
    /**
     * @var AccessHierarchyService
     */
    protected $classBeingTested;

    /**
     * @var AccessHierarchyFactory
     */
    protected $accessHierarchyFactory;

    /**
     * @var AccessFactory
     */
    protected $accessFactory;


    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(AccessHierarchyService::class);
        $this->accessFactory = $this->app->make(AccessFactory::class);
        $this->accessHierarchyFactory = $this->app->make(AccessHierarchyFactory::class);
    }

    public function test_assign_permission_to_role()
    {
        $permissionId = rand();
        $roleId = rand();

        $results = $this->classBeingTested->saveAccessHierarchy($permissionId, $roleId);

        $this->assertEquals([
            'id' => 1,
            'parent_id' => $permissionId,
            'child_id' => $roleId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableAccessHierarchy,
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
        $permission = $this->accessFactory->store();
        $role = $this->accessFactory->store();

        $rolePermission = $this->accessHierarchyFactory->store($permission['id'], $role['id']);

        $results = $this->classBeingTested->deleteAccessHierarchy($permission['id'], $role['id']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableAccessHierarchy,
            [
                'id' => $rolePermission['id'],
                'child_id' => $role['id'],
                'parent_id' => $permission['id']
            ]
        );
    }

    public function test_revoke_role_pemission_when_not_exist()
    {
        $results = $this->classBeingTested->deleteAccessHierarchy(rand(), rand());

        $this->assertNull($results);
    }

    public function test_role_has_permission_when_permission_exist()
    {
        $permission = $this->accessFactory->store();
        $role = $this->accessFactory->store();
        $this->accessHierarchyFactory->store($permission['id'], $role['id']);

        $results = $this->classBeingTested->accessHasChild($permission['id'], $role['id']);

        $this->assertTrue($results);
    }

    public function test_role_has_permission_when_permission_not_exist()
    {
        $results = $this->classBeingTested->accessHasChild(rand(), rand());

        $this->assertFalse($results);
    }
}
