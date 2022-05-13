<?php

namespace Railroad\Permissions\Tests\Decorators;

use Railroad\Permissions\Repositories\UserRoleRepository;
use Railroad\Permissions\Tests\PermissionsTestCase;
use Railroad\Resora\Entities\Entity;

class UserRoleDecoratorTest extends PermissionsTestCase
{
    /**
     * @var \Railroad\Permissions\Repositories\UserRoleRepository
     */
    protected $userRoleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRoleRepository = $this->app->make(UserRoleRepository::class);
    }

    public function test_create_user_role_decorator()
    {
        $userRole = $this->userRoleRepository->create([
                'user_id'    => rand(),
                'role'       => $this->faker->word,
                'created_at' => time(),
                'updated_at' => time()
            ]
        );
        $this->assertInstanceOf(Entity::class, $userRole);
    }

    public function test_decorate_none()
    {
        $userRole = $this->userRoleRepository->read(rand());

        $this->assertNull($userRole);
    }
}
