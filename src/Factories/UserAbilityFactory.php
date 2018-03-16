<?php

namespace Railroad\Permissions\Factories;

use Faker\Generator;
use Railroad\Permissions\Services\UserAbilityService;

class UserAbilityFactory extends UserAbilityService
{
    /**
     * @var Generator
     */
    protected $faker;

    public function store(
        $abilityId = null,
        $userId = null
    ) {
        $this->faker = app(Generator::class);

        $parameters =
            func_get_args() + [
                rand(),
                rand()
            ];
        return parent::assignAbilityToUser(...$parameters);
    }
}