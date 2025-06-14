<?php

namespace App\Services;

use App\Models\Role as RoleModel;

class RoleService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;
    private $roleModel;

    public function __construct(RoleModel $roleModel)
    {
        $this->roleModel = $roleModel;
    }

    public function getByName($name)
    {
        return $this->roleModel::where('name', $name)->whereNull('deleted_at');
    }

    public function getAllSortedId(){
        return $this->roleModel->where('id', '!=', 1)->get();
    }

    public function getPaginate($per_page, $keyword)
    {
        $container = $this->roleModel;
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('name', "like", "%" . $keyword . "%");
            });
        }
        return $container->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }

    public function get($id)
    {
        return $this->roleModel::where('id', $id);
    }

    public function create($data)
    {
        return $this->roleModel::create($data)->id;
    }

    public function update($id, $data)
    {
        return $this->roleModel::where(self::PRIMARY_KEY, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->roleModel::where(self::PRIMARY_KEY, $id)->delete();
    }

    public function restore($id)
    {
        return $this->roleModel::withTrashed()->find($id)->restore();
    }

    public function updatePrivilegesRole($role_id, $pids)
    {
        $adm_role = $this->roleModel->where('id', $role_id)->first();

        $attachedPIDs = $adm_role->privileges_ids()->get()->pluck('privilege_id')->toArray();

        $x = json_decode(json_encode($attachedPIDs), true);
        $y = is_array($pids) ? $pids : $pids->toArray();

        $newIds = array_diff($y, $x);
        $oldIds = array_diff($x, $y);

        if (!!$adm_role) {
            $adm_role->privileges()->attach($newIds);
            $adm_role->privileges()->detach($oldIds);
        }
    }

    public function deletePrivilegesRole($role_id)
    {
        $adm_role = $this->roleModel->where('id', $role_id)->first();
        $attachedPIDs = $adm_role->privileges_ids()->get()->pluck('privilege_id')->toArray();
        $oldIds = json_decode(json_encode($attachedPIDs), true);
        $adm_role->privileges()->detach($oldIds);
    }

    public function checkIsNameExist($role_name, $id)
    {
        $container = $this->roleModel;
        $container = $container->where('name', $role_name);
        if ($id) {
            $container = $container->where('id', '!=', $id);
        }

        return $container;
    }
}
