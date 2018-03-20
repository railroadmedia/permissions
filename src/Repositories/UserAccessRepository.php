<?php

namespace Railroad\Permissions\Repositories;


use Railroad\Permissions\Repositories\QueryBuilders\UserAbilityQueryBuilder;
use Railroad\Permissions\Services\ConfigService;

class UserAccessRepository extends RepositoryBase
{
    /**
     * @return UserAbilityQueryBuilder
     */
    protected function query()
    {
        return (new UserAbilityQueryBuilder(
            $this->connection(),
            $this->connection()->getQueryGrammar(),
            $this->connection()->getPostProcessor()
        ))
            ->from(ConfigService::$tableUserAccess);
    }

    /** Get user access based on user id and access slug
     * @param $userId
     * @param $abilitySlug
     * @return array
     */
    public function getUserAccess($userId, $abilitySlug)
    {
        return $this
            ->query()
            ->restrictUserIdAccess($userId)
            ->restrictByAccessSlug($abilitySlug)
            ->get()->toArray();
    }

    /** Check if user it's owner of the record with id
     * @param integer $userId
     * @param integer $id
     * @param string|false $table
     * @return bool
     */
    public function isOwner($userId, $id, $table = false)
    {
        if (!$table) {
            return false;
        }

        return $this->query()->from($table)->where([
                'user_id' => $userId,
                'id' => $id
            ])->count() > 0;
    }

    /** Check if user have permissions to access
     * @param $userId
     * @param $actions
     * @param $parameterNames
     * @return bool
     */
    public function can($userId, $actions, $parameterNames)
    {
        if (in_array('isOwner', $actions['permissions'])) {
            if ($this->isOwner($userId, request($parameterNames['0']), config('table_names')[$actions['as']])) {
                return true;
            }
        }

        foreach ($actions['permissions'] as $permission) {
            $userAccess = $this->getUserAccess($userId, $permission);
            if ($userAccess) {
                return true;
            }
        }

        return false;
    }

}