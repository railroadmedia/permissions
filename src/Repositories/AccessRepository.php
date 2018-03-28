<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class AccessRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()->table(ConfigService::$tableAccess);
    }

    public function getAccessBySlug($accessSlug, $brand =''){
        return $this->query()->where(
            [
                'slug' => $accessSlug,
                'brand' => $brand ?? ConfigService::$brand
            ]
        )->first();
    }

}