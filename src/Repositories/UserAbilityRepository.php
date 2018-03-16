<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Services\ConfigService;

class UserAbilityRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function query()
    {
        return $this->connection()
            ->table(ConfigService::$tableUserAbility);
    }

    public function getUserAbility($userId, $abilitySlug)
    {
        return $this->query()
            ->leftJoin(ConfigService::$tableAbility,
                ConfigService::$tableUserAbility . '.ability_id',
                '=',
                ConfigService::$tableAbility . '.id')
            ->where(ConfigService::$tableAbility . '.slug', $abilitySlug)
            ->where(ConfigService::$tableUserAbility . '.user_id', $userId)
            ->where(ConfigService::$tableAbility . '.brand', ConfigService::$brand)
            ->first();
    }

    public function userCan($userId, $permissionSlug, $brand)
    {

    }

}