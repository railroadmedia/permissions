<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\AccessHierarchyRepository;

class AccessHierarchyService
{
    /**
     * @var AccessHierarchyRepository
     */
    protected $accessHierarchyRepository;

    /**
     * AccessHierarchyService constructor.
     * @param AccessHierarchyRepository $accessHierarchyRepository
     */
    public function __construct(AccessHierarchyRepository $accessHierarchyRepository)
    {
        $this->accessHierarchyRepository = $accessHierarchyRepository;
    }


    /** Give an access to another access. E.g.:  give a permission to a role
     * @param int $parentId
     * @param int $childId
     * @return array
     */
    public function saveAccessHierarchy($parentId, $childId)
    {
        $abilityHierarchyId = $this->accessHierarchyRepository->create([
            'parent_id' => $parentId,
            'child_id' => $childId,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->accessHierarchyRepository->getById($abilityHierarchyId);
    }

    /** Delete an access hierarchy. Return null if the access hierarchy not exist
     * @param integer $parentId
     * @param integer $childId
     * @return bool|null
     */
    public function deleteAccessHierarchy($parentId, $childId)
    {
        $abilityHierarchy = $this->accessHierarchyRepository->getAccessChild($parentId, $childId);

        if (!$abilityHierarchy) {
            return null;
        }

        return $this->accessHierarchyRepository->delete($abilityHierarchy['id']) > 0;
    }

    /** Check if the access has the child assigned
     * @param $parentId
     * @param $childId
     * @return bool
     */
    public function accessHasChild($parentId, $childId)
    {
        return $this->accessHierarchyRepository->getAccessChild($parentId, $childId) > 0;
    }

    /** Call the repository method that return all the access children
     * @param $parentId
     * @return mixed
     */
    public function getAccessChildrens($parentId)
    {
        $parentIds = is_array($parentId)?$parentId:[$parentId];
        return $this->accessHierarchyRepository->getAccessChildren($parentIds);
    }

}