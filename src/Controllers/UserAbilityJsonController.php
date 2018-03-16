<?php

namespace Railroad\Permissions\Controllers;


use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AssignUserAbilityRequest;
use Railroad\Permissions\Requests\RevokeUserAbilityRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\UserAbilityService;


class UserAbilityJsonController extends Controller
{
    /**
     * @var UserAbilityService
     */
    private $userAbilityService;

    /**
     * UserAbilityJsonController constructor.
     * @param UserAbilityService $userAbilityService
     */
    public function __construct(UserAbilityService $userAbilityService)
    {
        $this->userAbilityService = $userAbilityService;
    }


    /** Call the method that assign ability to user
     * @param AssignUserAbilityRequest $request
     * @return JsonResponse
     */
    public function assignAbilityToUser(AssignUserAbilityRequest $request)
    {
        $permission = $this->userAbilityService->assignAbilityToUser(
            $request->get('ability_id'),
            $request->get('user_id')
        );

        return new JsonResponse($permission, 200);
    }

    /** Call the method that revoke user ability.
     *  Return: - NotFoundException if the user ability not exist in the database
     *          - JsonResponse with 204 code if the user ability was deleted
     * @param RevokeUserAbilityRequest $request
     * @return JsonResponse
     */
    public function revokeUserAbility(RevokeUserAbilityRequest $request)
    {
        $results = $this->userAbilityService->revokeUserAbility(
            $request->get('user_id'), $request->get('ability_slug')
        );

        //if the delete method response it's null the ability not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, user have not ability: ' . $request->get('ability_slug'))
        );

        return new JsonResponse(null, 204);
    }
}