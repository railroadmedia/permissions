<?php

namespace Railroad\Permissions\Tests\Functional\Controllers;

use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserRoleJsonControllerTest extends PermissionsTestCase
{
    public function setUp()
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
}
