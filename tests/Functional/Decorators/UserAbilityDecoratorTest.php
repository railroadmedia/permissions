<?php

namespace Railroad\Permissions\Tests\Decorators;

use Railroad\Permissions\Repositories\UserAbilityRepository;
use Railroad\Permissions\Tests\PermissionsTestCase;
use Railroad\Resora\Entities\Entity;

class UserAbilityDecoratorTest extends PermissionsTestCase
{
    /**
     * @var \Railroad\Permissions\Repositories\UserAbilityRepository
     */
    protected $userAbilityRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userAbilityRepository = $this->app->make(UserAbilityRepository::class);
    }

    public function test_decorate()
    {
        $userAbility = $this->userAbilityRepository->create([
            'user_id' => rand(),
            'ability' => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $this->assertInstanceOf(Entity::class, $userAbility);
    }

    public function test_decorate_none()
    {
        $userAbility = $this->userAbilityRepository->read(rand());


        $this->assertNull($userAbility);
    }
}
