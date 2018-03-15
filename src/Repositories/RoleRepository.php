<?php

namespace Railroad\Permissions\Repositories;


use Illuminate\Database\Query\Builder;
use Railroad\Permissions\Services\ConfigService;

class RoleRepository extends RepositoryBase
{

    /**
     * @return Builder
     */
    protected function query()
    {
        return $this->connection()->table(ConfigService::$tableRole);
    }
}