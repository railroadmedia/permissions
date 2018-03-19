<?php

namespace Railroad\Permissions\Controllers;

use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Requests\AccessCreateRequest;
use Railroad\Permissions\Requests\AccessUpdateRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Railroad\Permissions\Services\AccessService;

class AccessJsonController extends Controller
{
    /**
     * @var AccessService
     */
    private $accessService;

    /**
     * AccessJsonController constructor.
     * @param AccessService $accessService
     */
    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }


    /** Call the method that store an access record in the database and return the new created access in JSON format
     * @param AccessCreateRequest $request
     * @return JsonResponse
     */
    public function store(AccessCreateRequest $request)
    {
        $permission = $this->accessService->store(
            $request->get('name'),
            $request->get('slug'),
            $request->get('description'),
            $request->get('brand')
        );

        return new JsonResponse($permission, 200);
    }


    /** Call the method that modify an access.
     *  Return: - NotFoundException if the access not exist in the database
     *          - JsonResponse with the updated access
     * @param AccessUpdateRequest $request
     * @param int $abilityId
     * @return JsonResponse
     */
    public function update(AccessUpdateRequest $request, $abilityId)
    {
        //update access with the data sent on the request
        $ability = $this->accessService->update(
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

        //if the update method response it's null the access not exist; we throw the proper exception
        throw_if(
            is_null($ability),
            new NotFoundException('Update failed, access not found with id: ' . $abilityId)
        );

        return new JsonResponse($ability, 201);
    }

    /** Call the method that delete an access.
     *  Return: - NotFoundException if the access not exist in the database
     *          - JsonResponse with 204 code if the access was deleted
     * @param $abilityId
     * @return JsonResponse
     */
    public function delete($abilityId)
    {
        $results = $this->accessService->delete($abilityId);

        //if the delete method response it's null the access not exist; we throw the proper exception
        throw_if(
            is_null($results),
            new NotFoundException('Delete failed, access not found with id: ' . $abilityId)
        );

        return new JsonResponse(null, 204);
    }
}