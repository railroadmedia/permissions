<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class AbilityHierarchyRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()
            ->table(ConfigService::$tableAbilityHierarchy);
    }

    public function getAbilityChildren($parentId)
    {
        return $this->query()->whereIn('parent_id' , $parentId)->get()->toArray();
    }

    public function getAbilityChild($parentId, $childId)
    {
        return $this->query()->where(['parent_id' => $parentId,
            'child_id' => $childId])->get()->first();
    }


}