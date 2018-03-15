<?php

namespace Railroad\Permissions\Controllers;

use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\RoleCreateRequest;
use Railroad\Permissions\Requests\RoleUpdateRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\RoleService;


class RoleJsonController extends Controller
{
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * PermissionJsonController constructor.
     * @param $permissionService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /** Call the method that store a role in the database and return the new created role in JSON format
     * @param RoleCreateRequest $request
     * @return JsonResponse
     */
    public function store(RoleCreateRequest $request)
    {
        $role = $this->roleService->store(
            $request->get('name'),
            $request->get('slug'),
            $request->get('description'),
            $request->get('brand')
        );

        return new JsonResponse($role, 200);
    }

    /** Call the method that modify a role.
     *  Return: - NotFoundException if the role not exist in the database
     *          - JsonResponse with the updated role
     * @param RoleUpdateRequest $request
     * @param int $roleId
     * @return JsonResponse
     */
    public function update(RoleUpdateRequest $request, $roleId)
    {
        //update role with the data sent on the request
        $role = $this->roleService->update(
            $roleId,
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

        //if the update method response it's null the role not exist; we throw the proper exception
        throw_if(
            is_null($role),
            new NotFoundException('Update failed, role not found with id: ' . $roleId)
        );

        return new JsonResponse($role, 201);
    }

    /** Call the method that delete a role.
     *  Return: - NotFoundException if the role not exist in the database
     *          - JsonResponse with 204 code if the role was deleted
     * @param int $roleId
     * @return JsonResponse
     */
    public function delete($roleId)
    {
        $results = $this->roleService->delete($roleId);

        //if the delete method response it's null the role not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, role not found with id: ' . $roleId)
        );

        return new JsonResponse(null, 204);
    }
}