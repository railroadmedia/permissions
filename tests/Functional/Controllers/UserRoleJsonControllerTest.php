<?php


namespace Railroad\Permissions\Tests\Functional\Controllers;

use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Railroad\Permissions\Factories\RoleFactory;
use Railroad\Permissions\Factories\UserPermissionFactory;
use Railroad\Permissions\Factories\UserRoleFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserRoleJsonControllerTest extends PermissionsTestCase
{
    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    /**
     * @var UserRoleFactory
     */
    protected $userRoleFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->roleFactory = $this->app->make(RoleFactory::class);
        $this->userRoleFactory = $this->app->make(UserRoleFactory::class);
    }


    public function test_assign_role_to_user_validation()
    {
        $results = $this->call('PUT', 'assign-user-role', []);
        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "role_id",
                "detail" => "The role id field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_assign_role_to_user()
    {
        $role = $this->roleFactory->store();
        $userId = $this->createAndLogInNewUser();

        $results = $this->call('PUT', 'assign-user-role', [
            'role_id' => $role['id'],
            'user_id' => $userId
        ]);

        $this->assertEquals(200, $results->getStatusCode());

        $this->assertEquals([
            'id' => 1,
            'role_id' => $role['id'],
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results->decodeResponseJson()['results']);

        $this->assertDatabaseHas(
            ConfigService::$tableUserRole,
            [
                'id' => 1,
                'role_id' => $role['id'],
                'user_id' => $userId
            ]
        );
    }

    public function test_revoke_user_role_not_exist()
    {
        $results = $this->call('DELETE', 'user-role', [
            'role_slug' => $this->faker->slug
        ]);

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "role_slug",
                "detail" => "The selected role slug is invalid.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_revoke_user_role()
    {
        $role = $this->roleFactory->store();
        $userId = $this->createAndLogInNewUser();
        $userRole = $this->userRoleFactory->store($role['id'], $userId);

        $results = $this->call('DELETE', 'user-role', [
            'role_slug' =>$role['slug'],
            'user_id' => $userId
        ]);

        $this->assertEquals(204, $results->getStatusCode());

        $this->assertDatabaseMissing(
            ConfigService::$tableUserRole,
            [
                'id' => $userRole['id'],
                'role_id' => $role['id'],
                'user_id' =>$userId
            ]
        );
    }
}
