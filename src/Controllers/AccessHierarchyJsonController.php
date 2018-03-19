<?php

namespace Railroad\Permissions\Controllers;


use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AccessHierarchyRequest;
use Railroad\Permissions\Services\AccessHierarchyService;
use Railroad\Permissions\Responses\JsonResponse;


class AccessHierarchyJsonController extends Controller
{
    /**
     * @var AccessHierarchyService
     */
    private $accessHierarchyService;

    /**
     * AccessHierarchyJsonController constructor.
     * @param AccessHierarchyService $accessHierarchyService
     */
    public function __construct(AccessHierarchyService $accessHierarchyService)
    {
        $this->accessHierarchyService = $accessHierarchyService;
    }


    /** Call the method that save access hierarchy
     * @param AccessHierarchyRequest $request
     * @return JsonResponse
     */
    public function saveAccessHierarchy(AccessHierarchyRequest $request)
    {
        $role = $this->accessHierarchyService->saveAccessHierarchy(
            $request->get('parent_id'),
            $request->get('child_id')
        );

        return new JsonResponse($role, 200);
    }

    /** Call the method that delete access hierarchy.
     *  Return: - NotFoundException if the hierarchy  not exist in the database
     *          - JsonResponse with 204 code if the hierarchy was delete from the database
     * @param AccessHierarchyRequest $request
     * @return JsonResponse
     */
    public function deleteAccessHierarchy(AccessHierarchyRequest $request)
    {
        $results = $this->accessHierarchyService->deleteAccessHierarchy(
            $request->get('parent_id'), $request->get('child_id')
        );

        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, access have not the child: ' . $request->get('child_id'))
        );

        return new JsonResponse(null, 204);
    }
}