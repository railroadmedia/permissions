<?php

namespace Railroad\Permissions\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Repositories\UserRoleRepository;
use Railroad\Permissions\Requests\UserRoleCreateRequest;
use Railroad\Permissions\Requests\UserRolesDeleteRequest;
use Railroad\Permissions\Requests\UserRoleUpdateRequest;
use Railroad\Permissions\Requests\UserRolesCreateRequest;
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
     * @param $userId
     * @return JsonResponse
     */
    public function show($userId)
    {
        $usersRoles =
            $this->userRoleRepository->query()
                ->where('user_id', $userId)
                ->get()
                ->toArray();

        return new JsonResponse($usersRoles, 200);
    }

    /**
     * @param UserRoleCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserRoleCreateRequest $request)
    {
        $role = $this->userRoleRepository->create(
            array_merge(
                $request->only(['user_id', 'role']),
                [
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
        ));

        return new JsonResponse($role, 200);
    }

    /**
     * @param UserRolesCreateRequest $request
     * @return JsonResponse
     */
    public function storeMultiple(UserRolesCreateRequest $request)
    {
        $userId = $request->input('user_id');
        $roles = [];

        foreach ($request->input('roles') as $role) {
            $roles[] = $this->userRoleRepository->create([
                    'user_id' => $userId,
                    'role' => $role,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]);
        }

        return new JsonResponse($roles, 200);
    }

    /**
     * @param UserRoleUpdateRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UserRoleUpdateRequest $request, $id)
    {
        $role = $this->userRoleRepository->update(
            $id,
            $request->only(['user_id', 'role'])
        );

        throw_if(
            is_null($role),
            new NotFoundException('Update failed, user role not found with id: ' . $id)
        );

        return new JsonResponse($role, 201);
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

    /**
     * @param UserRolesDeleteRequest $request
     * @return JsonResponse
     */
    public function deleteMultiple(UserRolesDeleteRequest $request)
    {
        foreach ($request->input('roles') as $roleId) {
            $this->userRoleRepository->destroy($roleId);
        }

        return new JsonResponse(null, 204);
    }
}