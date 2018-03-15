<?php

namespace Railroad\Permissions\Factories;

use Faker\Generator;
use Railroad\Permissions\Services\UserPermissionService;
use Railroad\Permissions\Services\UserRoleService;

class UserRoleFactory extends UserRoleService
{
    /**
     * @var Generator
     */
    protected $faker;

    public function store(
        $roleId = null,
        $userId = null
    ) {
        $this->faker = app(Generator::class);

        $parameters =
            func_get_args() + [
                rand(),
                rand()
            ];
        return parent::assignRoleToUser(...$parameters);
    }
}