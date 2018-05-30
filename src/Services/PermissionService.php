<?php

namespace Railroad\Permissions\Services;

use Carbon\Carbon;
use Exception;
use Railroad\Permissions\Exceptions\NotAllowedException;
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

    /**
     * A memory cache so we only need to make a database query for a user once per request.
     *
     * @var array
     */
    private static $cache = [];

    /**
     * PermissionService constructor.
     *
     * @param UserAbilityRepository $userAbilityRepository
     * @param UserRoleRepository    $userRoleRepository
     */
    public function __construct(UserAbilityRepository $userAbilityRepository, UserRoleRepository $userRoleRepository)
    {
        $this->userAbilityRepository = $userAbilityRepository;
        $this->userRoleRepository    = $userRoleRepository;
    }

    /**
     * @param $userId
     * @param $ability
     * @return bool
     */
    public function can($userId, $ability)
    {

        if(isset(self::$cache[$userId]['abilities']) && (in_array($ability, self::$cache[$userId]['abilities'])))
        {
            return true;
        }
        elseif(isset(self::$cache[$userId]))
        {
            return false;
        }

        $this->cache($userId);

        return $this->can($userId, $ability);
    }

    /**
     * @param $userId
     * @param $ability
     * @param Exception|null $exception
     * @throws NotAllowedException
     * @throws Exception
     */
    public function canOrThrow($userId, $ability, Exception $exception = null)
    {
        if (!$this->can($userId, $ability)) {
            if (empty($exception)) {
                throw new NotAllowedException(
                    'You are not allowed to ' . str_replace('_', ' ', str_replace('.', ' ', $ability))
                );
            }

            throw $exception;
        }
    }

    /**
     * @param $userId
     * @param $role
     * @return bool
     */
    public function is($userId, $role)
    {
        if(isset(self::$cache[$userId]['roles'][$role]))
        {
            return true;
        }

        $usersRoles = $this->userRoleRepository->query()->where('user_id', $userId)->get();

        if($usersRoles->pluck('role')->search($role) !== false)
        {
            return true;
        }

        return false;
    }

    /**
     * @param int    $userId
     * @param string $ability
     */
    public function assignAbility($userId, $ability)
    {
        $exists = $this->userAbilityRepository->query()
            ->where(
                [
                    'user_id' => $userId,
                    'ability' => $ability,
                ]
            )
            ->exists();

        if(!$exists)
        {
            $this->userAbilityRepository->create(
                [
                    'user_id'    => $userId,
                    'ability'    => $ability,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );

            $this->clearCache();
        }
    }

    /**
     * @param int    $userId
     * @param string $role
     */
    public function assignRole($userId, $role)
    {
        $exists = $this->userRoleRepository->query()
            ->where(
                [
                    'user_id' => $userId,
                    'role'    => $role,
                ]
            )
            ->exists();

        if(!$exists)
        {
            $this->userRoleRepository->create(
                [
                    'user_id'    => $userId,
                    'role'       => $role,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );

            $this->clearCache();
        }
    }

    /**
     * @param int    $userId
     * @param string $ability
     */
    public function revokeAbility($userId, $ability)
    {
        $this->userAbilityRepository->query()->where(['user_id' => $userId, 'ability' => $ability])->delete();
    }

    /**
     * @param int    $userId
     * @param string $role
     */
    public function revokeRole($userId, $role)
    {
        $this->userRoleRepository->query()->where(['user_id' => $userId, 'role' => $role])->delete();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getAllUsersAbilities($userId)
    {
        if(isset(self::$cache[$userId]['abilities']))
        {
            return self::$cache[$userId]['abilities'];
        }

        $this->cache($userId);

        return $this->getAllUsersAbilities($userId);
    }

    /**
     * @return void
     */
    public function clearCache()
    {
        self::$cache = [];
    }

    /**
     * @param $userId
     */
    private function cache($userId)
    {
        $usersRoles     = $this->userRoleRepository->query()->where('user_id', $userId)->get();
        $usersAbilities = $this->userAbilityRepository->query()->where('user_id', $userId)->get();

        self::$cache[$userId]['abilities'] = [];
        self::$cache[$userId]['roles']     = [];

        foreach($usersRoles->pluck('role') as $usersRole)
        {
            self::$cache[$userId]['roles'][] = $usersRole;

            foreach(ConfigService::$roleAbilities[$usersRole] ?? [] as $roleAbility)
            {
                self::$cache[$userId]['abilities'][] = $roleAbility;
            }
        }
        foreach($usersAbilities->pluck('ability') as $userAbility)
        {
            self::$cache[$userId]['abilities'][] = $userAbility;
        }
    }
}