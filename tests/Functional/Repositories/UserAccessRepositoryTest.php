<?php

use Railroad\Permissions\Factories\AccessFactory;
use Railroad\Permissions\Factories\AccessHierarchyFactory;
use Railroad\Permissions\Factories\UserAccessFactory;
use Railroad\Permissions\Repositories\UserAccessRepository;
use PHPUnit\Framework\TestCase;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserAccessRepositoryTest extends PermissionsTestCase
{
    /**
     * @var AccessFactory
     */
    private $accessFactory;

    /**
     * @var UserAccessFactory
     */
    private $userAccessFactory;

    /**
     * @var AccessHierarchyFactory
     */
    private $accessHierarchyFactory;

    /**
     * @var UserAccessRepository
     */
    private $classBeingTested;

    protected function setUp()
    {
        parent::setUp();
        $this->accessFactory = $this->app->make(AccessFactory::class);
        $this->userAccessFactory = $this->app->make(UserAccessFactory::class);
        $this->accessHierarchyFactory = $this->app->make(AccessHierarchyFactory::class);
        $this->classBeingTested = $this->app->make(UserAccessRepository::class);
    }

    public function test_user_can_when_direct_access()
    {
        $access = $this->accessFactory->store();
        $userId = $this->createAndLogInNewUser();

        $this->userAccessFactory->assignAccessToUser($access['id'], $userId);

        $this->assertTrue($this->classBeingTested->can($userId, [
            'permissions' => [$access['slug']]
        ]));
    }

    public function test_user_can_when_no_rights()
    {
        $userId = $this->createAndLogInNewUser();

        $this->assertFalse($this->classBeingTested->can($userId, [
            'permissions' => [$this->faker->slug]
        ]));
    }

    public function test_user_can_when_no_user()
    {
        $this->assertFalse($this->classBeingTested->can(rand(), [
            'permissions' => [$this->faker->slug]
        ]));
    }

    public function test_user_can_with_inherited_access()
    {
        //save access hierarchy
        $access = $this->accessFactory->store();
        $ability = $this->accessFactory->store();
        $this->accessHierarchyFactory->store($access['id'], $ability['id']);

        $userId = $this->createAndLogInNewUser();
        $userAccess = $this->userAccessFactory->store($access['id'], $userId);

        $this->assertTrue($this->classBeingTested->can($userId, [
            'permissions' => [$ability['slug']]
        ]));
    }
}
