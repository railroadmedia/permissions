<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class UserAccessRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()
            ->table(ConfigService::$tableUserAccess);
    }

    public function getUserAccess($userId, $abilitySlug)
    {
        return $this->query()
            ->leftJoin(ConfigService::$tableAccess,
                ConfigService::$tableUserAccess . '.access_id',
                '=',
                ConfigService::$tableAccess . '.id')
            ->where(ConfigService::$tableAccess . '.slug', $abilitySlug)
            ->where(ConfigService::$tableUserAccess . '.user_id', $userId)
            ->where(ConfigService::$tableAccess . '.brand', ConfigService::$brand)
            ->first();
    }

    public function userCan($userId, $permissionSlug, $brand)
    {

    }

}