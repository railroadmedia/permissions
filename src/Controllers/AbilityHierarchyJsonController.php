<?php

namespace Railroad\Permissions\Controllers;


use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AbilityHierarchyRequest;
use Railroad\Permissions\Services\AbilityHierarchyService;
use Railroad\Permissions\Responses\JsonResponse;


class AbilityHierarchyJsonController extends Controller
{
    /**
     * @var AbilityHierarchyService
     */
    private $abilityHierarchyService;

    /**
     * AbilityHierarchyJsonController constructor.
     * @param AbilityHierarchyService $abilityHierarchyService
     */
    public function __construct(AbilityHierarchyService $abilityHierarchyService)
    {
        $this->abilityHierarchyService = $abilityHierarchyService;
    }


    /** Call the method that save ability hierarchy
     * @param AbilityHierarchyRequest $request
     * @return JsonResponse
     */
    public function saveAbilityHierarchy(AbilityHierarchyRequest $request)
    {
        $role = $this->abilityHierarchyService->saveAbilityHierarchy(
            $request->get('parent_id'),
            $request->get('child_id')
        );

        return new JsonResponse($role, 200);
    }

    /** Call the method that delete ability hierarchy.
     *  Return: - NotFoundException if the hierarchy  not exist in the database
     *          - JsonResponse with 204 code if the hierarchy was delete from the database
     * @param AbilityHierarchyRequest $request
     * @return JsonResponse
     */
    public function deleteAbilityHierarchy(AbilityHierarchyRequest $request)
    {
        $results = $this->abilityHierarchyService->deleteAbilityHierarchy(
            $request->get('parent_id'), $request->get('child_id')
        );

        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, ability have not the child: ' . $request->get('child_id'))
        );

        return new JsonResponse(null, 204);
    }
}