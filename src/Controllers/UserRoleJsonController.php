<?php

namespace Railroad\Permissions\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Repositories\UserRoleRepository;
use Railroad\Permissions\Requests\UserRoleCreateRequest;
use Railroad\Permissions\Requests\UserRoleUpdateRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Throwable;

class UserRoleJsonController extends Controller
{
    /**
     * @var UserRoleRepository
     */
    private $userRoleRepository;

    /**
     * AccessJsonController constructor.
     *
     * @param UserRoleRepository $userRoleRepository
     */
    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param UserRoleCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserRoleCreateRequest $request)
    {
        $ability = $this->userRoleRepository->create(
            array_merge(
                $request->only(['user_id', 'role']),
                [
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
        ));

        return new JsonResponse($ability, 200);
    }

    /**
     * @param UserRoleUpdateRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UserRoleUpdateRequest $request, $id)
    {
        $ability = $this->userRoleRepository->update(
            $id,
            $request->only(['user_id', 'role'])
        );

        throw_if(
            is_null($ability),
            new NotFoundException('Update failed, user role not found with id: ' . $id)
        );

        return new JsonResponse($ability, 201);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function delete(Request $request, $id)
    {
        $deleted = $this->userRoleRepository->destroy($id);

        return new JsonResponse(null, 204);
    }
}