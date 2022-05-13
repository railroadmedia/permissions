<?php

namespace Railroad\Permissions\Tests\Functional\Controllers;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Tests\PermissionsTestCase;

class UserAbilityJsonControllerTest extends PermissionsTestCase
{
    use ArraySubsetAsserts;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_store()
    {
        $userAbilityData = [
            'user_id' => rand(),
            'ability' => $this->faker->word
        ];

        $response = $this->call(
            'PUT',
            '/permissions/user-ability',
            $userAbilityData
        );

        $this->assertDatabaseHas(ConfigService::$tableUserAbilities, $userAbilityData);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_store_validation()
    {
        $response = $this->call(
            'PUT',
            'permissions/user-ability'
        );

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArraySubset([
            [
                'source' => 'user_id',
                'detail' => 'The user id field is required.'
            ],
            [
                'source' => 'ability',
                'detail' => 'The ability field is required.'
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_update_when_not_exist()
    {
        $userAbilityData = [
            'user_id' => rand(),
            'ability' => $this->faker->word
        ];

        $response = $this->call(
            'PATCH',
            'permissions/user-ability/' . rand(),
            $userAbilityData
        );

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_validation_rules()
    {
        $userAbilityData = [
            'user_id'    => rand(),
            'ability'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userAbilityId = $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insertGetId($userAbilityData);

        $incorrectData = [
            'user_id' => $this->faker->word,
            'ability' => rand()
        ];
        $response      = $this->call(
            'PATCH',
            'permissions/user-ability/' . $userAbilityId,
            $incorrectData
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_update()
    {
        $userAbilityData = [
            'user_id'    => rand(),
            'ability'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userAbilityId = $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insertGetId($userAbilityData);

        $updatedUserAbilityData = [
            'ability' => $this->faker->word
        ];

        $response = $this->call(
            'PATCH',
            'permissions/user-ability/' . $userAbilityId,
            $updatedUserAbilityData
        );

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas(ConfigService::$tableUserAbilities,
            array_merge($userAbilityData, $updatedUserAbilityData)
        );
        $this->assertDatabaseMissing(ConfigService::$tableUserAbilities,
            $userAbilityData
        );
    }

    public function test_delete()
    {
        $userAbilityData = [
            'user_id'    => rand(),
            'ability'    => $this->faker->word,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $userAbilityId = $this->databaseManager->table(ConfigService::$tableUserAbilities)
            ->insertGetId($userAbilityData);

        $response = $this->call(
            'DELETE',
            'permissions/user-ability/' . $userAbilityId
        );

        $this->assertEquals(204, $response->getStatusCode());

        $this->assertDatabaseMissing(ConfigService::$tableUserAbilities,
            $userAbilityData
        );
    }
}
