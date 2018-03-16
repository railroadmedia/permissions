<?php

namespace Railroad\Permissions\Factories;

use Faker\Generator;
use Railroad\Permissions\Services\AbilityHierarchyService;
use Railroad\Permissions\Services\UserPermissionService;

class AbilityHierarchyFactory extends AbilityHierarchyService
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
        return parent::saveAbilityHierarchy(...$parameters);
    }
}