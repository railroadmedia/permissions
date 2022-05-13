<?php

namespace Railroad\Permissions\Tests\Functional;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class PermissionServiceTest extends PermissionsTestCase
{
    /**
     * @var \Railroad\Permissions\Services\PermissionService
     */
    protected $classBeingTested;

    public function setUp(): void
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

    protected function setupColumnsTestConfig()
    {
        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $ability = 'update.users';
        $usersRolesToIds = [];

        // setup developer role
        $developerId = rand(1, 32767);
        $developerRole = 'developer';
        $role = [
            'user_id'    => $developerId,
            'role'       => $developerRole,
            'created_at' => $dateTime,
            'updated_at' => $dateTime
        ];
        $developerAbility = [
            'user_id'    => $developerId,
            'ability'    => $ability,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $usersRolesToIds[$developerRole] = $developerId;

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($role);
        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($developerAbility);

        // setup administrator role
        $administratorId = rand(1, 32767);
        $administratorRole = 'administrator';
        $role = [
            'user_id'    => $administratorId,
            'role'       => $administratorRole,
            'created_at' => $dateTime,
            'updated_at' => $dateTime
        ];
        $administratorAbility = [
            'user_id'    => $administratorId,
            'ability'    => $ability,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $usersRolesToIds[$administratorRole] = $administratorId;

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($role);
        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($administratorAbility);

        // setup user role
        $userId = rand(1, 32767);
        $userRole = 'user';
        $role = [
            'user_id'    => $userId,
            'role'       => $userRole,
            'created_at' => $dateTime,
            'updated_at' => $dateTime
        ];
        $userAbility = [
            'user_id'    => $userId,
            'ability'    => $ability,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $usersRolesToIds[$userRole] = $userId;

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($role);
        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($userAbility);

        // setup moderator role
        $moderatorId = rand(1, 32767);
        $moderatorRole = 'moderator';
        $role = [
            'user_id'    => $moderatorId,
            'role'       => $moderatorRole,
            'created_at' => $dateTime,
            'updated_at' => $dateTime
        ];
        $moderatorAbility = [
            'user_id'    => $moderatorId,
            'ability'    => $ability,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $usersRolesToIds[$moderatorRole] = $moderatorId;

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($role);
        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($moderatorAbility);

        // setup a default mode role, no columns configured, only for "->can()" ability test
        $defaultId = rand(1, 32767);
        $defaultRole = 'default';
        $role = [
            'user_id'    => $defaultId,
            'role'       => $defaultRole,
            'created_at' => $dateTime,
            'updated_at' => $dateTime
        ];
        $moderatorAbility = [
            'user_id'    => $defaultId,
            'ability'    => $ability,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $usersRolesToIds[$defaultRole] = $defaultId;

        $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insert($role);
        $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insert($moderatorAbility);

        // extend test columns config
        $configColumnAbilities = [
            $developerRole => [
                $ability => [
                    '*'
                ]
            ],
            $administratorRole => [
                $ability => [
                    'except' => [
                        'id',
                        'session_salt'
                    ]
                ],
                $this->faker->word,
                $this->faker->word => [
                    $this->faker->word,
                    $this->faker->word,
                    $this->faker->word
                ]
            ],
            $userRole => [
                $ability => [
                    'only' => [
                        'display_name'
                    ]
                ],
                $this->faker->word,
                $this->faker->word => [
                    $this->faker->word
                ],
                $this->faker->word,
                $this->faker->word => [
                    $this->faker->word,
                    $this->faker->word,
                    $this->faker->word
                ]
            ],
            $moderatorRole => [
                $ability => [
                    '*',
                    'only' => [
                        'display_name'
                    ],
                    'except' => [
                        'id',
                        'session_salt'
                    ]
                ],
                $this->faker->word,
                $this->faker->word => [
                    $this->faker->word
                ],
                $this->faker->word,
                $this->faker->word => [
                    $this->faker->word,
                    $this->faker->word,
                    $this->faker->word
                ]
            ]
        ];

        ConfigService::$roleAbilities += $configColumnAbilities;

        return [$ability, $usersRolesToIds, $configColumnAbilities];
    }

    public function test_columns_all()
    {
        list(
            $ability,
            $usersRolesToIds,
            $configColumnAbilities
        ) = $this->setupColumnsTestConfig();

        $developerRole = 'developer';
        $developerId = $usersRolesToIds[$developerRole];

        $unfilteredColumns = [
            'column1' => $this->faker->randomNumber(),
            'column2' => $this->faker->randomNumber(),
            'column3' => $this->faker->randomNumber(),
            'column4' => $this->faker->randomNumber()
        ];

        $filteredColumns = $this->classBeingTested
            ->columns($developerId, $ability, $unfilteredColumns);

        // '*' rule should return all columns
        $expectedColumns = $unfilteredColumns;

        $this->assertEquals($expectedColumns, $filteredColumns);
    }

    public function test_columns_except()
    {
        list(
            $ability,
            $usersRolesToIds,
            $configColumnAbilities
        ) = $this->setupColumnsTestConfig();

        $administratorRole = 'administrator';
        $administratorId = $usersRolesToIds[$administratorRole];

        $unfilteredColumns = [
            'id' => $this->faker->randomNumber(),
            'column1' => $this->faker->randomNumber(),
            'column2' => $this->faker->randomNumber(),
            'column3' => $this->faker->randomNumber(),
            'column4' => $this->faker->randomNumber(),
            'session_salt' => $this->faker->word
        ];

        $filteredColumns = $this->classBeingTested
            ->columns($administratorId, $ability, $unfilteredColumns);

        $expectedColumns = Arr::except(
            $unfilteredColumns,
            $configColumnAbilities[$administratorRole][$ability]['except']
        );

        $this->assertEquals($expectedColumns, $filteredColumns);
    }

    public function test_columns_only()
    {
        list(
            $ability,
            $usersRolesToIds,
            $configColumnAbilities
        ) = $this->setupColumnsTestConfig();

        $userRole = 'user';
        $userId = $usersRolesToIds[$userRole];

        $unfilteredColumns = [
            'id' => $this->faker->randomNumber(),
            'column1' => $this->faker->randomNumber(),
            'column2' => $this->faker->randomNumber(),
            'column3' => $this->faker->randomNumber(),
            'column4' => $this->faker->randomNumber(),
            'display_name' => $this->faker->word
        ];

        $filteredColumns = $this->classBeingTested
            ->columns($userId, $ability, $unfilteredColumns);

        $expectedColumns['display_name'] = $unfilteredColumns['display_name'];

        $this->assertEquals($expectedColumns, $filteredColumns);
    }

    public function test_columns_default()
    {
        list(
            $ability,
            $usersRolesToIds,
            $configColumnAbilities
        ) = $this->setupColumnsTestConfig();

        $defaultRole = 'default';
        $defaultId = $usersRolesToIds[$defaultRole];

        $unfilteredColumns = [
            'id' => $this->faker->randomNumber(),
            'column1' => $this->faker->randomNumber(),
            'column2' => $this->faker->randomNumber(),
            'column3' => $this->faker->randomNumber(),
            'column4' => $this->faker->randomNumber(),
            'display_name' => $this->faker->word
        ];

        $defaultCoumns = ['display_name'];

        $filteredColumns = $this->classBeingTested
            ->columns($defaultId, $ability, $unfilteredColumns, $defaultCoumns);

        $expectedColumns['display_name'] = $unfilteredColumns['display_name'];

        $this->assertEquals($expectedColumns, $filteredColumns);
    }
}
