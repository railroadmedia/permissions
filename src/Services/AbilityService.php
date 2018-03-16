<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\AbilityRepository;
use Railroad\Permissions\Requests\AbilityHierarchyRequest;

class AbilityService
{
    //constants for Role and Permission
    CONST ROLE_TYPE = 'role';
    CONST PERMISSION_TYPE = 'permission';
    /**
     * @var AbilityRepository
     */
    protected $abilityRepository;

    /**
     * AbilityService constructor.
     * @param AbilityRepository $abilityRepository
     */
    public function __construct(AbilityRepository $permissionRepository)
    {
        $this->abilityRepository = $permissionRepository;
    }

    /** Call the repository method that save the ability in the database and return an array with ability details.
     * @param string $name
     * @param string $slug
     * @param string $type (role or permission)
     * @param string $description
     * @param null $brand
     * @return array
     */
    public function store($name, $slug, $type, $description = '', $brand = null)
    {
        $permissionId = $this->abilityRepository->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'brand' => $brand ?? ConfigService::$brand,
            'type' => $type,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->getById($permissionId);
    }

    /** Return an array with the ability details.
     * @param integer $id
     * @return array
     */
    public function getById($id)
    {
        return $this->abilityRepository->getById($id);
    }

    /** If the ability exist in the database, call the repository method that update the ability and return an array with details.
     * If the ability not exist in the database return null.
     * @param integer $id
     * @param array $data
     * @return array|null
     */
    public function update($id, array $data)
    {
        $permission = $this->abilityRepository->getById($id);
        if (!$permission) {
            return null;
        }

        $data['updated_on'] = Carbon::now()->toDateTimeString();
        $this->abilityRepository->update($id, $data);

        return $this->getById($id);
    }

    /** If the ability exists call the method that delete it; otherwise return null.
     * @param integer $id
     * @return bool|null
     */
    public function delete($id)
    {
        $ability = $this->abilityRepository->getById($id);
        if (!$ability) {
            return null;
        }

        return $this->abilityRepository->delete($id);
    }

}