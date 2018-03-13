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

    public function getById($id)
    {
        return $this->permissionRepository->getById($id);
    }

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

    public function delete($id)
    {
        $permission = $this->permissionRepository->getById($id);
        if (!$permission) {
            return null;
        }

        return $this->permissionRepository->delete($id);
    }

}