<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    private $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $result = $this->roleService->getPaginate($per_page, $keyword);

        return $result;
    }

    public function listRoles()
    {
        $roles = $this->roleService->getAllSortedId();

        $roles->transform(function ($role) {
            return [
                'id' => $role->id,
                'value' => $role->id,
                'text' => $role->name,
            ];
        });

        return $roles;
    }

    public function get($id)
    {
        $container = $this->roleService->get($id);
        $data = $container->with('privileges_ids')->first();
        if ($data->privileges_ids != []) {
            $data->privileges_ids = $data->privileges_ids->pluck('privilege_id');
            $data->unsetRelation('privileges_ids');
        }
        return ResponseHelper::get($data);
    }

    public function checkIsNameExists(Request $request)
    {
        $role_name = $request->name;
        $id = $request->id;

        $existing = $this->roleService->checkIsNameExist($role_name, $id);

        if ($existing->first()) {
            return ResponseHelper::get(false);
        } else {
            return ResponseHelper::get(true);
        }
    }

    public function create(Request $request)
    {
        $existing = $this->roleService->getByName($request->name);
        if (count($existing->get())) {
            throw new CustomException("Role name already used!");
        }

        return DB::transaction(function () use ($request) {
            $data = $request->only(Schema::getColumnListing('roles'));

            $queryResult = $this->roleService->create($data);

            if (!!$queryResult) {

                $pids = [];
                if (!!$request->role_privileges && count($request->role_privileges) > 0) {
                    $pids = collect($request->role_privileges)->pluck('id');
                    $this->roleService->updatePrivilegesRole($queryResult, $pids);
                }

                return ResponseHelper::create($queryResult);
            }

            return false;
        });
    }

    public function update($id, Request $request)
    {
        $existing = $this->roleService->getByName($request->name);
        if (count($existing->where('id', '!=', $id)->get())) {
            throw new CustomException("Role name already used!");
        }

        return DB::transaction(function () use ($id, $request) {
            $data = $request->only(Schema::getColumnListing('roles'));

            $this->roleService->update($id, $data);

            $pids = [];
            if ($request->update_privileges) {
                if (!!$request->role_privileges && count($request->role_privileges) > 0) {
                    $pids = collect($request->role_privileges)->pluck('id');
                    $this->roleService->updatePrivilegesRole($id, $pids);
                } else {
                    $this->roleService->deletePrivilegesRole($id);
                }
            }

            return ResponseHelper::put();
        });
    }

    public function delete($id)
    {
        $this->roleService->delete($id);
        return ResponseHelper::delete();
    }

    public function restore($id)
    {
        return ResponseHelper::create($this->roleService->restore($id));
    }
}
