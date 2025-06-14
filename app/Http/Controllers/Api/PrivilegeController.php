<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Privilege;
use App\Services\PrivilegeService;
use Illuminate\Http\Request;

class PrivilegeController extends Controller
{
    private $privilegeService;

    public function __construct(PrivilegeService $privilegeService)
    {
        $this->privilegeService = $privilegeService;
    }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $result = $this->privilegeService->getPaginate($per_page, $keyword);

        return $result;
    }

    public function dataList(){
        $privileges = Privilege::get();

        $privileges->transform(function ($role) {
            return [
                'id' => $role->id,
                'value' => $role->id,
                'text' => $role->name,
            ];
        });

        return $privileges;
    }
}
