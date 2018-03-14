<?php

namespace Railroad\Permissions\Controllers;

use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\PermissionCreateRequest;
use Railroad\Permissions\Requests\PermissionUpdateRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\PermissionService;

class PermissionJsonController extends Controller
{
    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * PermissionJsonController constructor.
     * @param $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /** Call the method that store a permission in the database and return the new created permission in JSON format
     * @param PermissionCreateRequest $request
     * @return JsonResponse
     */
    public function store(PermissionCreateRequest $request)
    {
        $permission = $this->permissionService->store(
            $request->get('name'),
            $request->get('slug'),
            $request->get('description'),
            $request->get('brand')
        );

        return new JsonResponse($permission, 200);
    }

    /** Call the method that modify a permission.
     *  Return: - NotFoundException if the permission not exist in the database
     *          - JsonResponse with the updated permission
     * @param PermissionUpdateRequest $request
     * @param int $permissionId
     * @return JsonResponse
     */
    public function update(PermissionUpdateRequest $request, $permissionId)
    {
        //update permission with the data sent on the request
        $permission = $this->permissionService->update(
            $permissionId,
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

        //if the update method response it's null the permission not exist; we throw the proper exception
        throw_if(
            is_null($permission),
            new NotFoundException('Update failed, permission not found with id: ' . $permissionId)
        );

        return new JsonResponse($permission, 201);
    }

    /** Call the method that delete a permission.
     *  Return: - NotFoundException if the permission not exist in the database
     *          - JsonResponse with 204 code if the permission was deleted
     * @param $permissionId
     * @return JsonResponse
     */
    public function delete($permissionId)
    {
        $results = $this->permissionService->delete($permissionId);

        //if the delete method response it's null the permission not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, permission not found with id: ' . $permissionId)
        );

        return new JsonResponse(null, 204);
    }
}