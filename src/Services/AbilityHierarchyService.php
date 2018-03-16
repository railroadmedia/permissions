<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\AbilityHierarchyRepository;

class AbilityHierarchyService
{
    /**
     * @var AbilityHierarchyRepository
     */
    protected $abilityHierarchyRepository;

    /**
     * AbilityHierarchyService constructor.
     * @param AbilityHierarchyRepository $abilityHierarchyRepository
     */
    public function __construct(AbilityHierarchyRepository $abilityHierarchyRepository)
    {
        $this->abilityHierarchyRepository = $abilityHierarchyRepository;
    }

    /** Give a ability to another ability. E.g.:  give a permission to a role
     * @param int $parentId
     * @param int $childId
     * @return array
     */
    public function saveAbilityHierarchy($parentId, $childId)
    {
        $abilityHierarchyId = $this->abilityHierarchyRepository->create([
            'parent_id' => $parentId,
            'child_id' => $childId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->abilityHierarchyRepository->getById($abilityHierarchyId);
    }

    /** Delete an ability hierarchy. Return null if the ability hierarchy not exist
     * @param integer $parentId
     * @param integer $childId
     * @return bool|null
     */
    public function deleteAbilityHierarchy($parentId, $childId)
    {
        $abilityHierarchy = $this->abilityHierarchyRepository->getAbilityChild($parentId, $childId);

        if (!$abilityHierarchy) {
            return null;
        }

        return $this->abilityHierarchyRepository->delete($abilityHierarchy['id']) > 0;
    }

    /** Check if the ability has the child assigned
     * @param $parentId
     * @param $childId
     * @return bool
     */
    public function abilityHasChild($parentId, $childId)
    {
        return $this->abilityHierarchyRepository->getAbilityChild($parentId, $childId) > 0;
    }

    /** Call the repository method that return all the abilities children
     * @param $parentId
     * @return mixed
     */
    public function getAbilityChildrens($parentId)
    {
        $parentIds = is_array($parentId)?$parentId:[$parentId];
        return $this->abilityHierarchyRepository->getAbilityChildren($parentIds);
    }

}