<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\UserPermissionRepository;

class UserPermissionService
{
    protected $userPermissionRepository;

    /**
     * UserPermissionService constructor.
     * @param $userPermissionRepository
     */
    public function __construct(UserPermissionRepository $userPermissionRepository)
    {
        $this->userPermissionRepository = $userPermissionRepository;
    }

    public function assignPermissionToUser($permissionId, $userId)
    {
        $userPermissionId = $this->userPermissionRepository->create([
            'permission_id' => $permissionId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->userPermissionRepository->getById($userPermissionId);
    }

    public function revokeUserPermission($userPermissionId)
    {
        $userPermission = $this->userPermissionRepository->getById($userPermissionId);
        if (!$userPermission) {
            return null;
        }

        return $this->userPermissionRepository->delete($userPermissionId);
    }

}