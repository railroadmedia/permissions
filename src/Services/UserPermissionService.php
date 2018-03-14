<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\UserPermissionRepository;

class UserPermissionService
{
    protected $userPermissionRepository;

    /**
     * UserPermissionService constructor.
     * @param UserPermissionRepository $userPermissionRepository
     */
    public function __construct(UserPermissionRepository $userPermissionRepository)
    {
        $this->userPermissionRepository = $userPermissionRepository;
    }

    /** Give a permission to an user
     * @param int $permissionId
     * @param int $userId
     * @return array
     */
    public function assignPermissionToUser($permissionId, $userId)
    {
        $userPermissionId = $this->userPermissionRepository->create([
            'permission_id' => $permissionId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->userPermissionRepository->getById($userPermissionId);
    }

    /** Revoke an user permission. Return null if the user permission not exist
     * @param integer $userId
     * @param string $permissionSlug
     * @return bool|null
     */
    public function revokeUserPermission($userId, $permissionSlug)
    {
        $userPermission = $this->userPermissionRepository->getUserPermission($userId, $permissionSlug);
        if (!$userPermission) {
            return null;
        }

        return $this->userPermissionRepository->delete($userPermission['id']);
    }

    /** Check if the user has the permission assigned
     * @param integer $userId
     * @param string $permissionSlug
     * @return bool
     */
    public function userHasPermission($userId, $permissionSlug)
    {
        return $this->userPermissionRepository->getUserPermission($userId, $permissionSlug) > 0;
    }

}