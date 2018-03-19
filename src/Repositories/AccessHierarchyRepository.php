<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class AccessHierarchyRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()
            ->table(ConfigService::$tableAccessHierarchy);
    }

    public function getAccessChildren($parentId)
    {
        return $this->query()->whereIn('parent_id' , $parentId)->get()->toArray();
    }

    public function getAccessChild($parentId, $childId)
    {
        return $this->query()->where(['parent_id' => $parentId,
            'child_id' => $childId])->get()->first();
    }


}