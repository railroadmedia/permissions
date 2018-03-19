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

}