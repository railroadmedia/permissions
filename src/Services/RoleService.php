<?php


namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\RoleRepository;

class RoleService
{
    protected $roleRepository;

    /**
     * RoleService constructor.
     * @param $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /** Call the repository method that save the role in the database and return an array with role details.
     * @param $name
     * @param $slug
     * @param string $description
     * @param null $brand
     * @return array
     */
    public function store($name, $slug, $description = '', $brand = null)
    {
        $roleId = $this->roleRepository->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'brand' => $brand ?? ConfigService::$brand,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->getById($roleId);
    }

    /** Return an array with the role details.
     * @param integer $id
     * @return array
     */
    public function getById($id)
    {
        return $this->roleRepository->getById($id);
    }

    /** If the role exist in the database, call the repository method that update the role and return an array with role details.
     * If the role not exist in the database return null.
     * @param integer $id
     * @param array $data
     * @return array|null
     */
    public function update($id, array $data)
    {
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            return null;
        }

        $data['updated_on'] = Carbon::now()->toDateTimeString();
        $this->roleRepository->update($id, $data);

        return $this->getById($id);
    }

    /** If the role exists call the method that delete it; otherwise return null.
     * @param integer $id
     * @return bool|null
     */
    public function delete($id)
    {
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            return null;
        }

        return $this->roleRepository->delete($id);
    }

}