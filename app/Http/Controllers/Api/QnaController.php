<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseHelper;
use App\Services\QnaService;

class QnaController extends Controller
{
    private $QnaService;

    public function __construct(QnaService $QnaService)
    {
        $this->QnaService = $QnaService;
    }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $result = $this->QnaService->getPaginate($per_page, $keyword);

        return $result;
    }

    public function get($id)
    {
        $container = $this->QnaService->get($id);
        $data = $container->first();

        return ResponseHelper::get($data);
    }

    public function create(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->only(Schema::getColumnListing('qnas'));

            $queryResult = $this->QnaService->create($data);

            if (!!$queryResult) {
                return ResponseHelper::create($queryResult);
            }

            return false;
        });
    }

    public function update($id, Request $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $data = $request->only(Schema::getColumnListing('qnas'));

            $this->QnaService->update($id, $data);

            return ResponseHelper::put();
        });
    }

    public function delete($id)
    {
        $this->QnaService->delete($id);
        return ResponseHelper::delete();
    }
}
