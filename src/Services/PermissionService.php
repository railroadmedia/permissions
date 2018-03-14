<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\PermissionRepository;

class PermissionService
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * PermissionService constructor.
     * @param $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /** Call the repository method that save the permission in the database and return an array with permission details.
     * @param string $name
     * @param string $slug
     * @param string $description
     * @param null $brand
     * @return array
     */
    public function store($name, $slug, $description = '', $brand = null)
    {
        $permissionId = $this->permissionRepository->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'brand' => $brand ?? ConfigService::$brand,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->getById($permissionId);
    }

    /** Return an array with the permission details.
     * @param integer $id
     * @return array
     */
    public function getById($id)
    {
        return $this->permissionRepository->getById($id);
    }

    /** If the permission exist in the database, call the repository method that update the permission and return an array with permission details.
     * If the permission not exist in the database return null.
     * @param integer $id
     * @param array $data
     * @return array|null
     */
    public function update($id, array $data)
    {
        $permission = $this->permissionRepository->getById($id);
        if (!$permission) {
            return null;
        }

        $data['updated_on'] = Carbon::now()->toDateTimeString();
        $this->permissionRepository->update($id, $data);

        return $this->getById($id);
    }

    /** If the permission exists call the method that delete it; otherwise return null.
     * @param integer $id
     * @return bool|null
     */
    public function delete($id)
    {
        $permission = $this->permissionRepository->getById($id);
        if (!$permission) {
            return null;
        }

        return $this->permissionRepository->delete($id);
    }

}