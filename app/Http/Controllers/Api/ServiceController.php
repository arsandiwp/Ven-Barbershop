<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\FileHandlerService;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceController extends Controller
{
    private $serviceService;
    private $fileHandlerService;

    public function __construct(ServiceService $serviceService, FileHandlerService $fileHandlerService)
    {
        $this->serviceService = $serviceService;
        $this->fileHandlerService = $fileHandlerService;
    }

    // public function listFaqCategories()
    // {
    //     $faqCategories = $this->serviceService->getAll();

    //     $faqCategories->transform(function ($faqCategories) {
    //         return [
    //             'id' => $faqCategories->id,
    //             'value' => $faqCategories->id,
    //             'text' => $faqCategories->name,
    //             'photo' => $faqCategories->photo
    //         ];
    //     });

    //     return $faqCategories;
    // }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $result = $this->serviceService->getPaginate($per_page, $keyword);

        return $result;
    }

    public function listAllService(Request $request)
    {
        $keyword = @$request->keyword;
        return $this->serviceService->getAllCategory($keyword);
    }

    public function get($id) {
        $data = $this->serviceService->get($id);
        return ResponseHelper::get($data);
    }

    public function create(Request $request)
    {
        $existing = $this->serviceService->getByName($request->name);
        if (count($existing->get())) {
            throw new CustomException("Service name already used!");
        }

        $data = $request->only(Schema::getColumnListing('services'));

        if ($request->hasFile('photo')) {
            $path_photo = $this->fileHandlerService->saveFileToStorage($request->photo, '/service/' . $request->name);
            $data['photo'] = $path_photo;
        }

        return DB::transaction(function () use ($data, $request) {
            $queryResult = $this->serviceService->create($data);

            if ($queryResult) {
                return ResponseHelper::create($queryResult);
            }

            throw new CustomException("Error creating new Service!");
        });
    }

    public function update($id, Request $request)
    {
        $existing = $this->serviceService->getByName($request->name);
        if (count($existing->where('id', '!=', $id)->get())) {
            throw new CustomException("Service name already used!");
        }

        $existingData = $this->serviceService->get($id);

        $data = $request->only(Schema::getColumnListing('services'));

        if ($request->has('photo')) {
            $this->fileHandlerService->deleteFileItemDirectory('public/service/' . $existingData->name);
            $path_photo = $this->fileHandlerService->saveFileToStorage($request->photo, '/service/' . $request->name);
            $data['photo'] = $path_photo;
        }

        return DB::transaction(function () use ($data, $id, $request) {
            $queryResult = $this->serviceService->update($id, $data);

            return ResponseHelper::put($queryResult);
        });
    }

    public function delete($id)
    {
        // $isCategoryUsed = $this->serviceService->isCategoryUsed($id);

        // if ($isCategoryUsed) {
        //     throw new CustomException("Cannot delete category as it is used in FAQs");
        // }

        $this->serviceService->deleteCategoryItemStorage($id);
        $this->serviceService->delete($id);
        return ResponseHelper::delete();
    }

}
