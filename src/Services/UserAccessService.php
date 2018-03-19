<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\UserAccessRepository;

class UserAccessService
{
    protected $userAccessRepository;

    /**
     * UserAccessService constructor.
     * @param $userAccessRepository
     */
    public function __construct(UserAccessRepository $userAccessRepository)
    {
        $this->userAccessRepository = $userAccessRepository;
    }


    /** Give an access(role or permission) to an user
     * @param int $abilityId
     * @param int $userId
     * @return array
     */
    public function assignAccessToUser($abilityId, $userId)
    {
        $userAbilityId = $this->userAccessRepository->create([
            'access_id' => $abilityId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->userAccessRepository->getById($userAbilityId);
    }

    /** Revoke an user access. Return null if the user access not exist
     * @param integer $userId
     * @param string $abilitySlug
     * @return bool|null
     */
    public function revokeUserAccess($userId, $abilitySlug)
    {
        $userAbility = $this->userAccessRepository->getUserAccess($userId, $abilitySlug);
        if (!$userAbility) {
            return null;
        }

        return $this->userAccessRepository->delete($userAbility['id']);
    }

    /** Check if the user has the access assigned
     * @param integer $userId
     * @param string $abilitySlug
     * @return bool
     */
    public function userHasAccess($userId, $abilitySlug)
    {
        return $this->userAccessRepository->getUserAccess($userId, $abilitySlug) > 0;
    }

}