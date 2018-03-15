<?php

namespace Railroad\Permissions\Tests\Functional;


use Carbon\Carbon;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Factories\UserPermissionFactory;
use Railroad\Permissions\Factories\UserRoleFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\UserRoleService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserRoleServiceTest extends PermissionsTestCase
{
    /**
     * @var UserRoleService
     */
    protected $classBeingTested;

    /**
     * @var UserRoleFactory
     */
    protected $userRoleFactory;

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(UserRoleService::class);
        $this->roleFactory = $this->app->make(RoleFactory::class);
        $this->userRoleFactory = $this->app->make(UserRoleFactory::class);

    }

    public function test_assign_role_to_user()
    {
        $roleID = rand();
        $userId = rand();

        $results = $this->classBeingTested->assignRoleToUser($roleID, $userId);
        $this->assertEquals([
            'id' => 1,
            'role_id' => $roleID,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableUserRole,
            [
                'id' => 1,
                'role_id' => $roleID,
                'user_id' => $userId,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null
            ]
        );
    }

    public function test_revoke_user_role()
    {
        $role = $this->roleFactory->store();
        $userRole = $this->userRoleFactory->store($role['id']);

        $results = $this->classBeingTested->revokeUserRole($userRole['user_id'], $role['slug']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableUserRole,
            [
                'id' => $userRole['id'],
                'permission_id' => $userRole['role_id'],
                'user_id' => $userRole['user_id']
            ]
        );
    }

    public function test_revoke_user_role_when_not_exist()
    {
        $results = $this->classBeingTested->revokeUserRole(rand(), rand());

        $this->assertNull($results);
    }

    public function test_user_has_role()
    {
        $role = $this->roleFactory->store();
        $userRole = $this->userRoleFactory->store($role['id']);

        $results = $this->classBeingTested->userHasRole($userRole['user_id'], $role['slug']);
        $this->assertTrue($results);
    }

    public function test_user_has_role_when_role_not_exist()
    {
        $results = $this->classBeingTested->userHasRole(rand(), $this->faker->slug);
        $this->assertFalse($results);
    }
}
