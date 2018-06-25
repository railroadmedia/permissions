<?php

namespace Railroad\Permissions\Repositories;

use Railroad\Permissions\Services\ConfigService;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;

class UserRoleRepository extends RepositoryBase
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableUserRoles);
    }

    protected function decorate($results)
    {
        return Decorator::decorate($results, 'user_roles');
    }
}