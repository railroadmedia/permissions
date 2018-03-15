<?php

namespace Railroad\Permissions\Controllers;


use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AssignUserPermissionRequest;
use Railroad\Permissions\Requests\RevokeUserPermissionRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\UserPermissionService;

class UserPermissionJsonController extends Controller
{
    /**
     * @var UserPermissionService
     */
    private $userPermissionService;

    /**
     * UserPermissionJsonController constructor.
     * @param UserPermissionService $userPermissionService
     */
    public function __construct(UserPermissionService $userPermissionService)
    {
        $this->userPermissionService = $userPermissionService;
    }

    /** Call the method that assign permission to user
     * @param AssignUserPermissionRequest $request
     * @return JsonResponse
     */
    public function assignPermissionToUser(AssignUserPermissionRequest $request)
    {
        $permission = $this->userPermissionService->assignPermissionToUser(
            $request->get('permission_id'),
            $request->get('user_id')
        );

        return new JsonResponse($permission, 200);
    }

    /** Call the method that revoke user permission.
     *  Return: - NotFoundException if the user permission not exist in the database
     *          - JsonResponse with 204 code if the user permission was deleted
     * @param RevokeUserPermissionRequest $request
     * @return JsonResponse
     */
    public function revokeUserPermission(RevokeUserPermissionRequest $request)
    {
        $results = $this->userPermissionService->revokeUserPermission(
            $request->get('user_id'), $request->get('permission_slug')
        );

        //if the delete method response it's null the permission not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, user have not permission: ' . $request->get('permission_slug'))
        );

        return new JsonResponse(null, 204);
    }
}