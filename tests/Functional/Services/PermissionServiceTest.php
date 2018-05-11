<?php

namespace Railroad\Permissions\Tests\Functional;

use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class PermissionServiceTest extends PermissionsTestCase
{
    /**
     * @var \Railroad\Permissions\Services\PermissionService
     */
    protected $classBeingTested;

    public function setUp()
    {
        parent::setUp();
        $this->classBeingTested = $this->app->make(PermissionService::class);
    }

    public function test_get_all_users_abilities()
    {
        $userId   = $this->faker->numberBetween();
        $ability  = $this->faker->word;
        $ability2 = $this->faker->word;

        $userAbility = [
            [
                'user_id'    => $userId,
                'ability'    => $ability,
                'created_at' => time(),
                'updated_at' => time()
            ],
            [
                'user_id'    => $userId,
                'ability'    => $ability2,
                'created_at' => time(),
                'updated_at' => time()
            ]
        ];

        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($userAbility);

        $results = $this->classBeingTested->getAllUsersAbilities($userId);

        $this->assertEquals([$ability, $ability2], $results);
    }

    public function test_get_all_users_abilities_empty()
    {
        $results = $this->classBeingTested->getAllUsersAbilities(rand());

        $this->assertEmpty($results);
    }

    public function test_user_can_without_ability()
    {
        $this->assertFalse($this->classBeingTested->can(
            $this->faker->numberBetween(),
            $this->faker->word)
        );
    }

    public function test_user_can()
    {
        $userId  = $this->faker->numberBetween();
        $ability = $this->faker->word;

        $userAbility = [
            'user_id'    => $userId,
            'ability'    => $ability,
            'created_at' => time(),
            'updated_at' => time(),
        ];

        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($userAbility);

        $this->assertTrue($this->classBeingTested->can(
            $userId,
            $ability)
        );
    }

    public function test_revoke_role()
    {
        $userId = $this->faker->randomNumber();
        $role   = $this->faker->word;

        $userRole = [
            'user_id'    => $userId,
            'role'       => $role,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($userRole);

        $this->classBeingTested->revokeRole($userId, $role);

        $this->assertDatabaseMissing(ConfigService::$tableUserRoles,
            [
                'user_id' => $userId,
                'role'    => $role
            ]);
    }

    public function test_is_failed()
    {
        $this->assertFalse($this->classBeingTested->is(rand(), $this->faker->word));
    }

    public function test_is()
    {
        $userId = $this->faker->randomNumber();
        $role   = $this->faker->word;

        $userRole = [
            'user_id'    => $userId,
            'role'       => $role,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($userRole);

        $this->assertTrue($this->classBeingTested->is($userId, $role));
    }

    public function test_is_when_exists_multiple_roles()
    {
        $userId = $this->faker->randomNumber();
        $role   = $this->faker->word;

        $userRole = [[
            'user_id'    => $userId,
            'role'       => $role,
            'created_at' => time(),
            'updated_at' => time()
        ],[
            'user_id'    => $userId,
            'role'       => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ]];

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($userRole);

        $this->assertTrue($this->classBeingTested->is($userId, $role));
    }

    public function test_assign_ability()
    {
        $userId  = $this->faker->numberBetween();
        $ability = $this->faker->word;

        $this->classBeingTested->assignAbility($userId, $ability);

        $this->assertDatabaseHas(ConfigService::$tableUserAbilities,
            [
                'user_id' => $userId,
                'ability' => $ability
            ]);
    }

    public function test_revoke_ability()
    {
        $userId   = $this->faker->numberBetween();
        $ability  = $this->faker->word;
        $ability2 = $this->faker->word;

        $userAbility = [
            [
                'user_id'    => $userId,
                'ability'    => $ability,
                'created_at' => time(),
                'updated_at' => time()
            ],
            [
                'user_id'    => $userId,
                'ability'    => $ability2,
                'created_at' => time(),
                'updated_at' => time()
            ]
        ];

        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($userAbility);

        $this->classBeingTested->revokeAbility($userId, $ability2);

        $this->assertDatabaseMissing(ConfigService::$tableUserAbilities,
            [
                'user_id' => $userId,
                'ability' => $ability2
            ]);

        $this->assertEquals(1, count($this->classBeingTested->getAllUsersAbilities($userId)));
    }

    public function test_assign_role()
    {
        $userId = $this->faker->numberBetween();
        $role   = $this->faker->word;

        $this->classBeingTested->assignRole($userId, $role);

        $this->assertDatabaseHas(ConfigService::$tableUserRoles,
            [
                'user_id' => $userId,
                'role'    => $role
            ]);
    }
}
