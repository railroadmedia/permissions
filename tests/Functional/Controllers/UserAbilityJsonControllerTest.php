<?php


namespace Railroad\Permissions\Tests\Functional\Controllers;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Factories\UserAbilityFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserAbilityJsonControllerTest extends PermissionsTestCase
{
    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    /**
     * @var UserAbilityFactory
     */
    protected $userAbilityFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
        $this->userAbilityFactory = $this->app->make(UserAbilityFactory::class);
    }


    public function test_assign_ability_to_user_validation()
    {
        $results = $this->call('PUT', 'user-ability', []);
        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "ability_id",
                "detail" => "The ability id field is required.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_assign_ability_to_user()
    {
        $permission = $this->permissionFactory->store();
        $userId = $this->createAndLogInNewUser();

        $results = $this->call('PUT', 'user-ability', [
            'ability_id' => $permission['id'],
            'user_id' => $userId
        ]);

        $this->assertEquals(200, $results->getStatusCode());

        $this->assertEquals([
            'id' => 1,
            'ability_id' => $permission['id'],
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results->decodeResponseJson()['results']);

        $this->assertDatabaseHas(
            ConfigService::$tableUserAbility,
            [
                'id' => 1,
                'ability_id' => $permission['id'],
                'user_id' => $userId
            ]
        );
    }

    public function test_revoke_user_ability_not_exist()
    {
        $results = $this->call('DELETE', 'user-ability', [
            'ability_slug' => $this->faker->slug
        ]);

        $this->assertEquals(422, $results->getStatusCode());

        $this->assertEquals([
            [
                "source" => "user_id",
                "detail" => "The user id field is required.",
            ],
            [
                "source" => "ability_slug",
                "detail" => "The selected ability slug is invalid.",
            ]
        ], $results->decodeResponseJson()['errors']);
    }

    public function test_revoke_user_ability()
    {
        $permission = $this->permissionFactory->store();
        $userId = $this->createAndLogInNewUser();
        $userAbility = $this->userAbilityFactory->store($permission['id'], $userId);

        $results = $this->call('DELETE', 'user-ability', [
            'ability_slug' =>$permission['slug'],
            'user_id' => $userId
        ]);

        $this->assertEquals(204, $results->getStatusCode());

        $this->assertDatabaseMissing(
            ConfigService::$tableUserAbility,
            [
                'id' => $userAbility['id'],
                'ability_id' => $permission['id'],
                'user_id' =>$userId
            ]
        );
    }
}
