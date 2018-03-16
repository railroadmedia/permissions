<?php

namespace Railroad\Permissions\Controllers;

use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\PermissionCreateRequest;
use Railroad\Permissions\Requests\AbilityUpdateRequest;
use Railroad\Permissions\Requests\RoleCreateRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\AbilityService;

class AbilityJsonController extends Controller
{
    /**
     * @var AbilityService
     */
    private $abilityService;

    /**
     * AbilityJsonController constructor.
     * @param AbilityService $abilityService
     */
    public function __construct(AbilityService $abilityService)
    {
        $this->abilityService = $abilityService;
    }


    /** Call the method that store a permission in the database and return the new created permission in JSON format
     * @param PermissionCreateRequest $request
     * @return JsonResponse
     */
    public function storePermission(PermissionCreateRequest $request)
    {
        $permission = $this->abilityService->store(
            $request->get('name'),
            $request->get('slug'),
            AbilityService::PERMISSION_TYPE,
            $request->get('description'),
            $request->get('brand')
        );

        return new JsonResponse($permission, 200);
    }

    /** Call the method that store a role in the database and return the new created role in JSON format
     * @param RoleCreateRequest $request
     * @return JsonResponse
     */
    public function storeRole(RoleCreateRequest $request)
    {
        $role = $this->abilityService->store(
            $request->get('name'),
            $request->get('slug'),
            AbilityService::ROLE_TYPE,
            $request->get('description'),
            $request->get('brand')
        );

        return new JsonResponse($role, 200);
    }

    /** Call the method that modify an ability.
     *  Return: - NotFoundException if the ability not exist in the database
     *          - JsonResponse with the updated ability
     * @param AbilityUpdateRequest $request
     * @param int $abilityId
     * @return JsonResponse
     */
    public function update(AbilityUpdateRequest $request, $abilityId)
    {
        //update ability with the data sent on the request
        $ability = $this->abilityService->update(
            $abilityId,
            array_intersect_key(
                $request->all(),
                [
                    'name' => '',
                    'slug' => '',
                    'description' => '',
                    'brand' => ''
                ]
            )
        );

        //if the update method response it's null the ability not exist; we throw the proper exception
        throw_if(
            is_null($ability),
            new NotFoundException('Update failed, ability not found with id: ' . $abilityId)
        );

        return new JsonResponse($ability, 201);
    }

    /** Call the method that delete an ability.
     *  Return: - NotFoundException if the ability not exist in the database
     *          - JsonResponse with 204 code if the ability was deleted
     * @param $abilityId
     * @return JsonResponse
     */
    public function delete($abilityId)
    {
        $results = $this->abilityService->delete($abilityId);

        //if the delete method response it's null the ability not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, ability not found with id: ' . $abilityId)
        );

        return new JsonResponse(null, 204);
    }
}