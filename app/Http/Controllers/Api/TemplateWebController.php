<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseHelper;
use App\Services\TemplateWebService;

class TemplateWebController extends Controller
{
    private $TemplateWebService;

    public function __construct(TemplateWebService $TemplateWebService)
    {
        $this->TemplateWebService = $TemplateWebService;
    }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $result = $this->TemplateWebService->getPaginate($per_page, $keyword);

        return $result;
    }

    public function getList()
    {
        $datas = $this->TemplateWebService->getAll();

        $datas->transform(function ($role) {
            return [
                'id' => $role->id,
                'value' => $role->id,
                'text' => $role->title,
            ];
        });

        return $datas;
    }

    public function get($id)
    {
        $container = $this->TemplateWebService->get($id);
        $data = $container->first();

        return ResponseHelper::get($data);
    }

    public function create(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->only(Schema::getColumnListing('template_webs'));

            $queryResult = $this->TemplateWebService->create($data);

            if (!!$queryResult) {
                return ResponseHelper::create($queryResult);
            }

            return false;
        });
    }

    public function update($id, Request $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $data = $request->only(Schema::getColumnListing('template_webs'));

            $this->TemplateWebService->update($id, $data);

            return ResponseHelper::put();
        });
    }

    public function delete($id)
    {
        $this->TemplateWebService->delete($id);
        return ResponseHelper::delete();
    }
}
