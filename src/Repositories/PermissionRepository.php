<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class PermissionRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()->table(ConfigService::$tablePermissions);
    }
}