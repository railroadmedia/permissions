<?php

namespace Railroad\Permissions\Factories;


use Faker\Generator;
use Railroad\Permissions\Services\AbilityService;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\RoleService;

class RoleFactory extends AbilityService
{
    /**
     * @var Generator
     */
    protected $faker;

    public function store(
        $name = '',
        $slug = '',
        $type = '',
        $description = '',
        $brand = ''
    ) {
        $this->faker = app(Generator::class);

        $parameters =
            func_get_args() + [
                $this->faker->word,
                $this->faker->slug,
                AbilityService::ROLE_TYPE,
                $this->faker->text,
                ConfigService::$brand
            ];
        return parent::store(...$parameters);
    }
}