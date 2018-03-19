<?php

namespace Railroad\Permissions\Controllers;


use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AssignUserAccessRequest;
use Railroad\Permissions\Requests\RevokeUserAccessRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\UserAccessService;


class UserAccessJsonController extends Controller
{
    /**
     * @var UserAccessService
     */
    private $userAccessService;

    /**
     * UserAccessJsonController constructor.
     * @param UserAccessService $userAccessService
     */
    public function __construct(UserAccessService $userAccessService)
    {
        $this->userAccessService = $userAccessService;
    }


    /** Call the method that assign access to user
     * @param AssignUserAccessRequest $request
     * @return JsonResponse
     */
    public function assignAccessToUser(AssignUserAccessRequest $request)
    {
        $permission = $this->userAccessService->assignAccessToUser(
            $request->get('access_id'),
            $request->get('user_id')
        );

        return new JsonResponse($permission, 200);
    }

    /** Call the method that revoke user access.
     *  Return: - NotFoundException if the user access not exist in the database
     *          - JsonResponse with 204 code if the user access was deleted
     * @param RevokeUserAccessRequest $request
     * @return JsonResponse
     */
    public function revokeUserAccess(RevokeUserAccessRequest $request)
    {
        $results = $this->userAccessService->revokeUserAccess(
            $request->get('user_id'), $request->get('access_slug')
        );

        //if the delete method response it's null the ability not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, user have not access to: ' . $request->get('access_slug'))
        );

        return new JsonResponse(null, 204);
    }
}