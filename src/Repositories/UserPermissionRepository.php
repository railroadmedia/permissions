<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class UserPermissionRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()
            ->table(ConfigService::$tableUserPermission);
    }

    public function getUserPermission($userId, $permissionSlug)
    {
        return $this->query()
            ->leftJoin(ConfigService::$tablePermissions,
                ConfigService::$tableUserPermission . '.permission_id',
                '=',
                ConfigService::$tablePermissions . '.id')
            ->where(ConfigService::$tablePermissions . '.slug', $permissionSlug)
            ->where(ConfigService::$tableUserPermission . '.user_id', $userId)
            ->where(ConfigService::$tablePermissions.'.brand', ConfigService::$brand)
            ->first();
    }

}