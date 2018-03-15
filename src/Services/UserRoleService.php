<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\UserRoleRepository;

class UserRoleService
{
    protected $userRoleRepository;

    /**
     * UserRoleService constructor.
     * @param UserRoleRepository $userRoleRepository
     */
    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }


    /** Give a role to an user
     * @param int $roleId
     * @param int $userId
     * @return array
     */
    public function assignRoleToUser($roleId, $userId)
    {
        $userRoleId = $this->userRoleRepository->create([
            'role_id' => $roleId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->userRoleRepository->getById($userRoleId);
    }

    /** Revoke an user role. Return null if the user have not assigned the role
     * @param integer $userId
     * @param string $roleSlug
     * @return bool|null
     */
    public function revokeUserRole($userId, $roleSlug)
    {
        $userRole = $this->userRoleRepository->getUserRole($userId, $roleSlug);
        if (!$userRole) {
            return null;
        }

        return $this->userRoleRepository->delete($userRole['id']);
    }

    /** Check if the user has the role assigned
     * @param integer $userId
     * @param string $roleSlug
     * @return bool
     */
    public function userHasRole($userId, $roleSlug)
    {
        return $this->userRoleRepository->getUserRole($userId, $roleSlug) > 0;
    }

    public function getUserRoles($userId)
    {
        return $this->userRoleRepository->getUserRoles($userId);
    }
}