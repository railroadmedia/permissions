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
            ->get()->first();
    }

    /** Check if user it's owner of the record with id
     * @param integer $userId
     * @param integer $id
     * @param string|false $table
     * @return bool
     */
    public function isOwner($userId, $id, $routeName)
    {
        $ownerColumn = 'user_id';
        $columnName = 'id';
        $table = config('table_names')[$routeName];

        if(is_array(config('table_names')[$routeName]))
        {
            $table = config('table_names')[$routeName]['user_id'];
        }

        if ((!$userId) && (request()->filled('customer_id'))) {
            $ownerColumn = 'customer_id';
            $userId = request()->get('customer_id');
            if(is_array(config('table_names')[$routeName])) {
                $table = config('table_names')[$routeName]['customer_id'];
            }
        }

        if (array_key_exists($routeName, config('column_names'))) {
            $columnName = config('column_names')[$routeName];
        }

        if (!$table) {
            return false;
        }

        return $this->query()->from($table)->where([
                $ownerColumn => $userId,
                $columnName => $id
            ])->count() > 0;
    }

    /** Check if user have permissions to access
     * @param $userId
     * @param $actions
     * @param $parameterNames
     * @return bool
     */
    public function can($userId, $actions, $parameterNames = '')
    {

        if (empty($parameterNames)) {
            $parameterNames = current(request()->all());
        } else {
            $parameterNames = request()->route($parameterNames['0']);
        }

        if (in_array('isOwner', $actions['permissions'])) {

            if ($this->isOwner($userId, $parameterNames, $actions['as'])) {
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