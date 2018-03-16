<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\PermissionFactory;
use Railroad\Permissions\Factories\UserAbilityFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\UserAbilityService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserAbilityServiceTest extends PermissionsTestCase
{
    /**
     * @var UserAbilityService
     */
    protected $classBeingTested;

    /**
     * @var UserAbilityFactory
     */
    protected $userAbilityFactory;

    /**
     * @var PermissionFactory
     */
    protected $permissionFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(UserAbilityService::class);
        $this->permissionFactory = $this->app->make(PermissionFactory::class);
        $this->userAbilityFactory = $this->app->make(UserAbilityFactory::class);

    }

    public function test_assign_ability_to_user()
    {
        $abilityId = rand();
        $userId = rand();

        $results = $this->classBeingTested->assignAbilityToUser($abilityId, $userId);
        $this->assertEquals([
            'id' => 1,
            'ability_id' => $abilityId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableUserAbility,
            [
                'id' => 1,
                'ability_id' => $abilityId,
                'user_id' => $userId,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null
            ]
        );
    }

    public function test_revoke_user_ability()
    {
        $ability = $this->permissionFactory->store();
        $userAbility = $this->userAbilityFactory->store($ability['id']);

        $results = $this->classBeingTested->revokeUserAbility($userAbility['user_id'], $ability['slug']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableUserAbility,
            [
                'id' => $userAbility['id'],
                'ability_id' => $ability['id'],
                'user_id' => $userAbility['user_id']
            ]
        );
    }

    public function test_revoke_user_ability_when_not_exist()
    {
        $results = $this->classBeingTested->revokeUserAbility(rand(), rand());

        $this->assertNull($results);
    }

    public function test_user_has_ability()
    {
        $ability = $this->permissionFactory->store();
        $userAbility = $this->userAbilityFactory->store($ability['id']);

        $results = $this->classBeingTested->userHasAbility($userAbility['user_id'], $ability['slug']);
        $this->assertTrue($results);
    }

    public function test_user_has_ability_when_ability_not_exist()
    {
        $results = $this->classBeingTested->userHasAbility(rand(), $this->faker->slug);
        $this->assertFalse($results);
    }
}
