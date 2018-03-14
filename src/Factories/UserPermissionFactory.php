<?php

namespace Railroad\Permissions\Factories;

use Faker\Generator;
use Railroad\Permissions\Services\UserPermissionService;

class UserPermissionFactory extends UserPermissionService
{
    /**
     * @var Generator
     */
    protected $faker;

    public function store(
        $permissionId = null,
        $userId = null
    ) {
        $this->faker = app(Generator::class);

        $parameters =
            func_get_args() + [
                rand(),
                rand()
            ];
        return parent::assignPermissionToUser(...$parameters);
    }
}