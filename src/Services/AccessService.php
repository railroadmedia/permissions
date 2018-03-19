<?php

namespace Railroad\Permissions\Services;


use Carbon\Carbon;
use Railroad\Permissions\Repositories\AccessRepository;


class AccessService
{
    /**
     * @var AccessRepository
     */
    protected $accessRepository;

    /**
     * AccessService constructor.
     * @param AccessRepository $accessRepository
     */
    public function __construct(AccessRepository $accessRepository)
    {
        $this->accessRepository = $accessRepository;
    }


    /** Call the repository method that save the access in the database and return an array with access details.
     * @param string $name
     * @param string $slug
     * @param string $description
     * @param null $brand
     * @return array
     */
    public function store($name, $slug,  $description = '', $brand = null)
    {
        $permissionId = $this->accessRepository->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'brand' => $brand ?? ConfigService::$brand,
            'created_on' => Carbon::now()->toDateTimeString()
        ]);

        return $this->getById($permissionId);
    }

    /** Return an array with the access details.
     * @param integer $id
     * @return array
     */
    public function getById($id)
    {
        return $this->accessRepository->getById($id);
    }

    /** If the access exist in the database, call the repository method that update the access and return an array with details.
     * If the access not exist in the database return null.
     * @param integer $id
     * @param array $data
     * @return array|null
     */
    public function update($id, array $data)
    {
        $permission = $this->accessRepository->getById($id);
        if (!$permission) {
            return null;
        }

        $data['updated_on'] = Carbon::now()->toDateTimeString();
        $this->accessRepository->update($id, $data);

        return $this->getById($id);
    }

    /** If the access exists call the method that delete it; otherwise return null.
     * @param integer $id
     * @return bool|null
     */
    public function delete($id)
    {
        $ability = $this->accessRepository->getById($id);
        if (!$ability) {
            return null;
        }

        return $this->accessRepository->delete($id);
    }

}