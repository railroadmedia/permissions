<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Railroad\Permissions\Factories\AccessFactory;
use Railroad\Permissions\Factories\UserAccessFactory;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\UserAccessService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserAccessServiceTest extends PermissionsTestCase
{
    /**
     * @var UserAccessService
     */
    protected $classBeingTested;

    /**
     * @var UserAccessFactory
     */
    protected $userAccessyFactory;

    /**
     * @var AccessFactory
     */
    protected $accessFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(UserAccessService::class);
        $this->accessFactory = $this->app->make(AccessFactory::class);
        $this->userAccessyFactory = $this->app->make(UserAccessFactory::class);

    }

    public function test_assign_access_to_user()
    {
        $abilityId = rand();
        $userId = rand();

        $results = $this->classBeingTested->assignAccessToUser($abilityId, $userId);
        $this->assertEquals([
            'id' => 1,
            'access_id' => $abilityId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString(),
            'updated_on' => null
        ], $results);

        $this->assertDatabaseHas(
            ConfigService::$tableUserAccess,
            [
                'id' => 1,
                'access_id' => $abilityId,
                'user_id' => $userId,
                'created_on' => Carbon::now()->toDateTimeString(),
                'updated_on' => null
            ]
        );
    }

    public function test_revoke_user_access()
    {
        $ability = $this->accessFactory->store();
        $userAbility = $this->userAccessyFactory->store($ability['id']);

        $results = $this->classBeingTested->revokeUserAccess($userAbility['user_id'], $ability['slug']);

        $this->assertTrue($results);

        $this->assertDatabaseMissing(
            ConfigService::$tableUserAccess,
            [
                'id' => $userAbility['id'],
                'access_id' => $ability['id'],
                'user_id' => $userAbility['user_id']
            ]
        );
    }

    public function test_revoke_user_access_when_not_exist()
    {
        $results = $this->classBeingTested->revokeUserAccess(rand(), rand());

        $this->assertNull($results);
    }

    public function test_user_has_access()
    {
        $ability = $this->accessFactory->store();
        $userAbility = $this->userAccessyFactory->store($ability['id']);

        $results = $this->classBeingTested->userHasAccess($userAbility['user_id'], $ability['slug']);
        $this->assertTrue($results);
    }

    public function test_user_has_acess_when_access_not_exist()
    {
        $results = $this->classBeingTested->userHasAccess(rand(), $this->faker->slug);
        $this->assertFalse($results);
    }
}
