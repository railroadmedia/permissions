<?php

namespace Railroad\Permissions\Tests\Functional\Controllers;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserRoleJsonControllerTest extends PermissionsTestCase
{
    use ArraySubsetAsserts;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_store()
    {
        $userRoleData = [
            'user_id' => rand(),
            'role' => $this->faker->word
        ];

        $response = $this->call(
            'PUT',
            '/permissions/user-role',
            $userRoleData
        );

        $this->assertDatabaseHas(ConfigService::$tableUserRoles, $userRoleData);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_store_validation()
    {
        $response = $this->call(
            'PUT',
            'permissions/user-role'
        );

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArraySubset([
            [
                'source' => 'user_id',
                'detail' => 'The user id field is required.'
            ],
            [
                'source' => 'role',
                'detail' => 'The role field is required.'
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_store_multiple()
    {
        $userId = rand();
        $roles = [$this->faker->word, $this->faker->word, $this->faker->word];

        $requestData = [
            'user_id' => $userId,
            'roles' => $roles
        ];

        $response = $this->call(
            'PUT',
            '/permissions/user-roles',
            $requestData
        );

        $this->assertEquals(200, $response->getStatusCode());

        foreach ($roles as $role) {
            $this->assertDatabaseHas(
                ConfigService::$tableUserRoles,
                [
                    'user_id' => $userId,
                    'role' => $role,
                ]
            );
        }
    }

    public function test_store_multiple_validation()
    {
        $response = $this->call(
            'PUT',
            '/permissions/user-roles',
            []
        );

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertArraySubset([
            [
                'source' => 'user_id',
                'detail' => 'The user id field is required.'
            ],
            [
                'source' => 'roles',
                'detail' => 'The roles field is required.'
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_update_when_not_exist()
    {
        $userRoleData = [
            'user_id' => rand(),
            'role' => $this->faker->word
        ];

        $response = $this->call(
            'PATCH',
            'permissions/user-role/' . rand(),
            $userRoleData
        );

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_validation_rules()
    {
        $userRoleData = [
            'user_id'    => rand(),
            'role'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userRoleId = $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insertGetId($userRoleData);

        $incorrectData = [
            'user_id' => $this->faker->word,
            'role' => rand()
        ];
        $response      = $this->call(
            'PATCH',
            'permissions/user-role/' . $userRoleId,
            $incorrectData
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_update()
    {
        $userRoleData = [
            'user_id'    => rand(),
            'role'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userRoleId = $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insertGetId($userRoleData);

        $updatedUserRoleData = [
            'role' => $this->faker->word
        ];

        $response = $this->call(
            'PATCH',
            'permissions/user-role/' . $userRoleId,
            $updatedUserRoleData
        );

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas(ConfigService::$tableUserRoles,
            array_merge($userRoleData, $updatedUserRoleData)
        );
        $this->assertDatabaseMissing(ConfigService::$tableUserRoles,
            $userRoleData
        );
    }

    public function test_delete()
    {
        $userRoleData = [
            'user_id'    => rand(),
            'role'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userRoleId = $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insertGetId($userRoleData);

        $response = $this->call(
            'DELETE',
            'permissions/user-role/' . $userRoleId
        );

        $this->assertEquals(204, $response->getStatusCode());

        $this->assertDatabaseMissing(ConfigService::$tableUserRoles,
            $userRoleData
        );
    }

    public function test_delete_multiple()
    {
        $userRoleOneData = [
            'user_id'    => rand(),
            'role'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userRoleOneId = $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insertGetId($userRoleOneData);

        $userRoleTwoData = [
            'user_id'    => rand(),
            'role'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userRoleTwoId = $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insertGetId($userRoleTwoData);

        $userRoleThreeData = [
            'user_id'    => rand(),
            'role'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userRoleThreeId = $this->databaseManager->table(ConfigService::$tableUserRoles)
            ->insertGetId($userRoleThreeData);

        $response = $this->call(
            'DELETE',
            'permissions/user-roles',
            [
                'roles' => [$userRoleOneId, $userRoleTwoId]
            ]
        );

        $this->assertEquals(204, $response->getStatusCode());

        $this->assertDatabaseMissing(ConfigService::$tableUserRoles,
            $userRoleOneData
        );

        $this->assertDatabaseMissing(ConfigService::$tableUserRoles,
            $userRoleTwoData
        );

        $this->assertDatabaseHas(
            ConfigService::$tableUserRoles,
            $userRoleThreeData
        );
    }

    public function test_delete_multiple_validation()
    {
        $response = $this->call(
            'DELETE',
            '/permissions/user-roles',
            []
        );

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertArraySubset([
            [
                'source' => 'roles',
                'detail' => 'The roles field is required.'
            ]
        ], $response->decodeResponseJson()['errors']);
    }
}
