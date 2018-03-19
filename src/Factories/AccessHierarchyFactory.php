<?php

namespace Railroad\Permissions\Factories;

use Faker\Generator;
use Railroad\Permissions\Services\AccessHierarchyService;

class AccessHierarchyFactory extends AccessHierarchyService
{
    /**
     * @var Generator
     */
    protected $faker;

    public function store(
        $parentId = null,
        $childId = null
    ) {
        $this->faker = app(Generator::class);

        $parameters =
            func_get_args() + [
                rand(),
                rand()
            ];
        return parent::saveAccessHierarchy(...$parameters);
    }
}