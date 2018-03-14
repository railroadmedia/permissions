<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Factories\UserPermissionFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\UserPermissionService;
use PHPUnit\Framework\TestCase;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserPermissionServiceTest extends PermissionsTestCase
{
    /**
     * @var UserPermissionService
     */
    protected $classBeingTested;

    /**
     * @var UserPermissionFactory
     */
    protected $userPermissionFactory;

    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(UserPermissionService::class);
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
        $this->userPermissionFactory = $this->app->make(UserPermissionFactory::class);

    }

    public function test_assign_permission_to_user()
    {
        $permissionId = rand();
        $userId = rand();

        $results = $this->classBeingTested->assignPermissionToUser($permissionId, $userId);
        $this->assertEquals([
            'id' => 1,
            'permission_id' => $permissionId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableUserPermission,
            [
                'id' => 1,
                'permission_id' => $permissionId,
                'user_id' => $userId,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null
            ]
        );
    }

    public function test_revoke_user_permission()
    {
        $permission = $this->permissionFactory->store();
        $userPermission = $this->userPermissionFactory->store($permission['id']);

        $results = $this->classBeingTested->revokeUserPermission($userPermission['user_id'], $permission['slug']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableUserPermission,
            [
                'id' => $userPermission['id'],
                'permission_id' => $userPermission['permission_id'],
                'user_id' => $userPermission['user_id']
            ]
        );
    }

    public function test_revoke_user_pemission_when_not_exist()
    {
        $results = $this->classBeingTested->revokeUserPermission(rand(), rand());

        $this->assertNull($results);
    }

    public function test_user_has_permission_when_permission_exist()
    {
        $permission = $this->permissionFactory->store();
        $userPermission = $this->userPermissionFactory->store($permission['id']);

        $results = $this->classBeingTested->userHasPermission($userPermission['user_id'], $permission['slug']);
        $this->assertTrue($results);
    }

    public function test_user_has_permission_when_permission_not_exist()
    {
        $results = $this->classBeingTested->userHasPermission(rand(), $this->faker->slug);
        $this->assertFalse($results);
    }
}
