<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class UserRoleRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()
            ->table(ConfigService::$tableUserRole);
    }

    public function getUserRole($userId, $roleSlug)
    {
        return $this->query()
            ->leftJoin(ConfigService::$tableRole,
                ConfigService::$tableUserRole . '.role_id',
                '=',
                ConfigService::$tableRole . '.id')
            ->where(ConfigService::$tableRole . '.slug', $roleSlug)
            ->where(ConfigService::$tableUserRole . '.user_id', $userId)
            ->where(ConfigService::$tableRole.'.brand', ConfigService::$brand)
            ->first();
    }

    public function getUserRoles($userId)
    {
        return $this->query()->where('user_id', $userId)->get()->toArray();
    }

}