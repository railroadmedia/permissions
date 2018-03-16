<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\UserAbilityRepository;

class UserAbilityService
{
    protected $userAbilityRepository;

    /**
     * UserAbilityService constructor.
     * @param UserAbilityRepository $userAbilityRepository
     */
    public function __construct(UserAbilityRepository $userAbilityRepository)
    {
        $this->userAbilityRepository = $userAbilityRepository;
    }

    /** Give an ability(role or permission) to an user
     * @param int $abilityId
     * @param int $userId
     * @return array
     */
    public function assignAbilityToUser($abilityId, $userId)
    {
        $userAbilityId = $this->userAbilityRepository->create([
            'ability_id' => $abilityId,
            'user_id' => $userId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->userAbilityRepository->getById($userAbilityId);
    }

    /** Revoke an user ability. Return null if the user ability not exist
     * @param integer $userId
     * @param string $abilitySlug
     * @return bool|null
     */
    public function revokeUserAbility($userId, $abilitySlug)
    {
        $userAbility = $this->userAbilityRepository->getUserAbility($userId, $abilitySlug);
        if (!$userAbility) {
            return null;
        }

        return $this->userAbilityRepository->delete($userAbility['id']);
    }

    /** Check if the user has the ability assigned
     * @param integer $userId
     * @param string $abilitySlug
     * @return bool
     */
    public function userHasAbility($userId, $abilitySlug)
    {
        return $this->userAbilityRepository->getUserAbility($userId, $abilitySlug) > 0;
    }

}