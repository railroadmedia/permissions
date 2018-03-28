<?php


namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\AccessFactory;
use Railroad\Permissions\Factories\UserAccessFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserAccessJsonControllerTest extends PermissionsTestCase
{
    /**
     * @var AccessFactory
     */
    protected $accessFactory;

    /**
     * @var UserAccessFactory
     */
    protected $userAccessFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->accessFactory = $this->app->make(AccessFactory::class);
        $this->userAccessFactory = $this->app->make(UserAccessFactory::class);
    }


    public function test_assign_access_to_user_validation()
    {
        $results = $this->call('PUT', 'permissions/user-access', []);
        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "access_id",
                "detail" => "The access id field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_assign_access_to_user()
    {
        $permission = $this->accessFactory->store();
        $userId = $this->createAndLogInNewUser();

        $results = $this->call('PUT', 'permissions/user-access', [
            'access_id' => $permission['id'],
            'user_id' => $userId
        ]);

        $this->assertEquals(200, $results->getStatusCode());

        $this->assertEquals([
            'id' => 1,
            'access_id' => $permission['id'],
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results->decodeResponseJson()['results']);

        $this->assertDatabaseHas(
            ConfigService::$tableUserAccess,
            [
                'id' => 1,
                'access_id' => $permission['id'],
                'user_id' => $userId
            ]
        );
    }

    public function test_revoke_user_access_when_not_exist()
    {
        $results = $this->call('DELETE', 'permissions/user-access', [
            'access_slug' => $this->faker->slug
        ]);

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "access_slug",
                "detail" => "The selected access slug is invalid.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_revoke_user_access()
    {
        $permission = $this->accessFactory->store();
        $userId = $this->createAndLogInNewUser();
        $userAbility = $this->userAccessFactory->store($permission['id'], $userId);

        $results = $this->call('DELETE', 'permissions/user-access', [
            'access_slug' =>$permission['slug'],
            'user_id' => $userId
        ]);

        $this->assertEquals(204, $results->getStatusCode());

        $this->assertDatabaseMissing(
            ConfigService::$tableUserAccess,
            [
                'id' => $userAbility['id'],
                'access_id' => $permission['id'],
                'user_id' =>$userId
            ]
        );
    }
}
