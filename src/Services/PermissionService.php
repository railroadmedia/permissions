<?php

namespace Railroad\Permissions\Services;

use Railroad\Permissions\Repositories\UserAbilityRepository;
use Railroad\Permissions\Repositories\UserRoleRepository;

class PermissionService
{
    /**
     * @var UserAbilityRepository
     */
    private $userAbilityRepository;
    /**
     * @var UserRoleRepository
     */
    private $userRoleRepository;

    private static $cache = [];

    /**
     * PermissionService constructor.
     *
     * @param UserAbilityRepository $userAbilityRepository
     * @param UserRoleRepository $userRoleRepository
     */
    public function __construct(UserAbilityRepository $userAbilityRepository, UserRoleRepository $userRoleRepository)
    {
        $this->userAbilityRepository = $userAbilityRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param $userId
     * @param $ability
     * @return bool
     */
    public function can($userId, $ability)
    {
        if (isset(self::$cache[$userId]['abilities'][$ability])) {
            return true;
        } elseif (isset(self::$cache[$userId])) {
            return false;
        }

        $usersRoles = $this->userRoleRepository->query()->where('user_id', $userId)->get();
        $usersAbilities = $this->userAbilityRepository->query()->where('user_id', $userId)->get();

        $this->cache($userId);

        return $this->can($userId, $ability);
    }

    /**
     * @param $userId
     * @param $role
     * @return bool
     */
    public function is($userId, $role)
    {
        if (isset(self::$cache[$userId]['roles'][$role])) {
            return true;
        }

        $usersRoles = $this->userRoleRepository->query()->where('user_id', $userId)->get();

        if ($usersRoles->pluck('role')->search($role) !== false) {
            return true;
        }

        return false;
    }

    public function getAllUsersAbilities($userId)
    {
        if (isset(self::$cache[$userId]['abilities'])) {
            return self::$cache[$userId]['abilities'];
        }

        $this->cache($userId);

        return $this->getAllUsersAbilities($userId);
    }

    public function clearCache()
    {
        self::$cache = [];
    }

    /**
     * @param $userId
     */
    private function cache($userId)
    {
        $usersRoles = $this->userRoleRepository->query()->where('user_id', $userId)->get();
        $usersAbilities = $this->userAbilityRepository->query()->where('user_id', $userId)->get();

        self::$cache[$userId]['abilities'] = [];
        self::$cache[$userId]['roles'] = [];

        foreach ($usersRoles->pluck('role') as $usersRole) {
            self::$cache[$userId]['roles'][] = $usersRole;

            foreach (ConfigService::$roleAbilities[$usersRole] as $roleAbility) {
                self::$cache[$userId]['abilities'][] = $roleAbility;
            }
        }

        foreach ($usersAbilities->pluck('role') as $userAbility) {
            self::$cache[$userId]['abilities'][] = $userAbility;
        }
    }
}