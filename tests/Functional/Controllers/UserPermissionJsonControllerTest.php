<?php


namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Factories\UserPermissionFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserPermissionJsonControllerTest extends PermissionsTestCase
{
    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    /**
     * @var UserPermissionFactory
     */
    protected $userPermissionFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
        $this->userPermissionFactory = $this->app->make(UserPermissionFactory::class);
    }


    public function test_assign_permission_to_user_validation()
    {
        $results = $this->call('PUT', 'assign-permission', []);
        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "permission_id",
                "detail" => "The permission id field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_assign_permission_to_user()
    {
        $permission = $this->permissionFactory->store();
        $userId = $this->createAndLogInNewUser();

        $results = $this->call('PUT', 'assign-permission', [
            'permission_id' => $permission['id'],
            'user_id' => $userId
        ]);

        $this->assertEquals(200, $results->getStatusCode());

        $this->assertEquals([
            'id' => 1,
            'permission_id' => $permission['id'],
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results->decodeResponseJson()['results']);

        $this->assertDatabaseHas(
            ConfigService::$tableUserPermission,
            [
                'id' => 1,
                'permission_id' => $permission['id'],
                'user_id' => $userId
            ]
        );
    }

    public function test_revoke_user_permission_not_exist()
    {
        $results = $this->call('DELETE', 'user-permission', [
            'permission_slug' => $this->faker->slug
        ]);

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "permission_slug",
                "detail" => "The selected permission slug is invalid.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_revoke_user_permission()
    {
        $permission = $this->permissionFactory->store();
        $userId = $this->createAndLogInNewUser();
        $userPemission = $this->userPermissionFactory->store($permission['id'], $userId);

        $results = $this->call('DELETE', 'user-permission', [
            'permission_slug' =>$permission['slug'],
            'user_id' => $userId
        ]);

        $this->assertEquals(204, $results->getStatusCode());

        $this->assertDatabaseMissing(
            ConfigService::$tableUserPermission,
            [
                'id' => $userPemission['id'],
                'permission_id' => $permission['id'],
                'user_id' =>$userId
            ]
        );
    }
}
