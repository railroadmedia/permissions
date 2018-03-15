<?php

namespace Railroad\Permissions\Controllers;


use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AssignUserPermissionRequest;
use Railroad\Permissions\Requests\AssignUserRoleRequest;
use Railroad\Permissions\Requests\RevokeUserPermissionRequest;
use Railroad\Permissions\Requests\RevokeUserRoleRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\UserPermissionService;
use Railroad\Permissions\Services\UserRoleService;

class UserRoleJsonController extends Controller
{
    /**
     * @var UserRoleService
     */
    private $userRoleService;

    /**
     * UserRoleJsonController constructor.
     * @param UserRoleService $userRoleService
     */
    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }


    /** Call the method that assign role to user
     * @param AssignUserRoleRequest $request
     * @return JsonResponse
     */
    public function assignRoleToUser(AssignUserRoleRequest $request)
    {
        $role = $this->userRoleService->assignRoleToUser(
            $request->get('role_id'),
            $request->get('user_id')
        );

        return new JsonResponse($role, 200);
    }

    /** Call the method that revoke user role.
     *  Return: - NotFoundException if the user role not exist in the database
     *          - JsonResponse with 204 code if the user role was deleted
     * @param RevokeUserRoleRequest $request
     * @return JsonResponse
     */
    public function revokeUserRole(RevokeUserRoleRequest $request)
    {
        $results = $this->userRoleService->revokeUserRole(
            $request->get('user_id'), $request->get('role_slug')
        );

        //if the delete method response it's null the role not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, user have not assigned role: ' . $request->get('role_slug'))
        );

        return new JsonResponse(null, 204);
    }
}