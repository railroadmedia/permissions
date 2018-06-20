<?php

namespace Railroad\Permissions\Decorators;

use Railroad\Permissions\Repositories\UserAbilityRepository;
use Railroad\Permissions\Repositories\UserRoleRepository;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class UserPermissionsDecorator implements DecoratorInterface
{
    /**
     * @var UserRoleRepository
     */
    private $userRoleRepository;

    /**
     * @var UserAbilityRepository
     */
    private $userAbilityRepository;

    /**
     * UserPermissions constructor.
     *
     * @param UserRoleRepository $userRoleRepository
     * @param UserAbilityRepository $userAbilityRepository
     */
    public function __construct(UserRoleRepository $userRoleRepository, UserAbilityRepository $userAbilityRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
        $this->userAbilityRepository = $userAbilityRepository;
    }

    /**
     * @param BaseCollection $users
     * @return BaseCollection
     */
    public function decorate($users)
    {
        $userIds = $users->pluck('id');

        $usersRoles = $this->userRoleRepository->query()->whereIn('user_id', $userIds)->get();
        $usersAbilities = $this->userAbilityRepository->query()->whereIn('user_id', $userIds)->get();

        foreach ($users as $userIndex => $user) {
            $users[$userIndex]['permissions'] = [];

            foreach ($usersRoles as $userRoleIndex => $userRole) {

                if ($userRole['user_id'] == $user['id']) {
                    $users[$userIndex]['permissions']['roles'][] = $userRole;
                }

            }

            foreach ($usersAbilities as $userAbilityIndex => $userAbility) {

                if ($userAbility['user_id'] == $user['id']) {
                    $users[$userIndex]['permissions']['abilities'][] = $userAbility;
                }

            }
        }

        return $users;
    }
}