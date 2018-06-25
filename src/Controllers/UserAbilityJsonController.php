<?php

namespace Railroad\Permissions\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotFoundException;
use Railroad\Permissions\Repositories\UserAbilityRepository;
use Railroad\Permissions\Requests\UserAbilityCreateRequest;
use Railroad\Permissions\Requests\UserAbilityUpdateRequest;
use Railroad\Permissions\Responses\JsonResponse;
use Throwable;

class UserAbilityJsonController extends Controller
{
    /**
     * @var UserAbilityRepository
     */
    private $userAbilityRepository;

    /**
     * UserAbilityJsonController constructor.
     *
     * @param UserAbilityRepository $userAbilityRepository
     */
    public function __construct(UserAbilityRepository $userAbilityRepository)
    {
        $this->userAbilityRepository = $userAbilityRepository;
    }

    /**
     * @param UserAbilityCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserAbilityCreateRequest $request)
    {
        $ability = $this->userAbilityRepository->create(
            array_merge(
                $request->only(['user_id', 'ability']),
                [
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
        ));

        return new JsonResponse($ability, 200);
    }

    /**
     * @param UserAbilityUpdateRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UserAbilityUpdateRequest $request, $id)
    {
        $ability = $this->userAbilityRepository->update(
            $id,
            $request->only(['user_id', 'ability'])
        );

        throw_if(
            is_null($ability),
            new NotFoundException('Update failed, access not found with id: ' . $id)
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
        $deleted = $this->userAbilityRepository->destroy($id);

        return new JsonResponse(null, 204);
    }
}