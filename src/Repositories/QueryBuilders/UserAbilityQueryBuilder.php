<?php

namespace Railroad\Permissions\Repositories\QueryBuilders;


use Illuminate\Database\Query\Builder;
use Railroad\Permissions\Services\ConfigService;

class UserAbilityQueryBuilder extends Builder
{

    public function restrictUserIdAccess($userId)
    {
        $this->where('user_id', $userId);

        return $this;
    }


    /**
     * @param string $slugHierarchy
     * @return $this
     */
    public function restrictByAccessSlug($slugHierarchy)
    {
        $this->where(
            function (Builder $builder) use ($slugHierarchy) {
                $builder->whereIn(
                    ConfigService::$tableUserAccess . '.access_id',
                    function (Builder $builder) use ($slugHierarchy) {
                        $builder
                            ->select(['inherited_access.id'])
                            ->from(ConfigService::$tableAccess)
                            ->leftJoin(
                                ConfigService::$tableAccessHierarchy . ' as ' . 'inheritance',
                                'inheritance' . '.child_id',
                                '=',
                                ConfigService::$tableAccess . '.id'
                            )
                            ->leftJoin(
                                ConfigService::$tableAccess . ' as ' . 'inherited_access',
                                'inherited_access' . '.id',
                                '=',
                                'inheritance' . '.parent_id'
                            )->where('access.slug', $slugHierarchy);

                    }
                )
                    ->orWhere(ConfigService::$tableUserAccess . '.access_id',
                        function (Builder $builder) use ($slugHierarchy) {
                            $builder
                                ->select([ConfigService::$tableAccess . '.id'])
                                ->from(ConfigService::$tableAccess)
                                ->where('slug', $slugHierarchy);
                        });
            });
        return $this;
    }


}