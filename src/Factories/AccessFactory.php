<?php

namespace Railroad\Permissions\Factories;


use Faker\Generator;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Permissions\Services\AccessService;

class AccessFactory extends AccessService
{
    /**
     * @var Generator
     */
    protected $faker;

    public function store(
        $name = '',
        $slug = '',
        $description = '',
        $brand = ''
    ) {
        $this->faker = app(Generator::class);

        $parameters =
            func_get_args() + [
                $this->faker->word,
                $this->faker->slug,
                $this->faker->text,
                ConfigService::$brand
            ];
        return parent::store(...$parameters);
    }
}