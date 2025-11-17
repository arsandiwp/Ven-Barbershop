<?php

namespace App\Services;

use App\Models\Service;
use App\Services\FileHandlerService;

class ServiceService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;
    private $service;
    private $fileHandlerService;


    public function __construct(Service $service, FileHandlerService $fileHandlerService)
    {
        $this->service = $service;
        $this->fileHandlerService = $fileHandlerService;
    }

    public function getPaginate($per_page, $keyword, $status = null)
    {
        $container = $this->service;
        if (!empty($status)) {
            $container = $container->where('status', $status);
        }

        if (!empty($keyword)) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }
        return $container->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }

    public function getAll()
    {
        return $this->service::all();
    }

    public function getAllCategory($keyword)
    {
        $container = $this->service;
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
                $q->where('description', 'like', '%' . $keyword . '%');
            });
        }

        return $container->get();

    }

    public function get($id)
    {
        // return $this->service::where(self::PRIMARY_KEY, $id)->with('faq')->first();
        return $this->service::where(self::PRIMARY_KEY, $id)->first();
    }

    public function create($data)
    {
        return $this->service::create($data)->id;
    }

    public function update($id, $data)
    {
        return $this->service::where(self::PRIMARY_KEY, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->service::where(self::PRIMARY_KEY, $id)->delete();
    }

    public function getByName($name)
    {
        return $this->service->where('name', $name)->whereNull('deleted_at');
    }

    // public function isCategoryUsed($categoryId)
    // {
    //     $category = $this->service->with('faq')->find($categoryId);

    //     if ($category) {
    //         return $category->faq->isNotEmpty();
    //     }

    //     return false;
    // }

    public function deleteCategoryItemStorage($id)
    {
        $category = $this->service->find($id);

        $name = $category->name;
        $deleted = $this->service::where(self::PRIMARY_KEY, $id)->delete();

        if ($deleted) {
            $this->fileHandlerService->deleteFileItemDirectory("public/service/" . $name);
            return 1;
        }

        return 0;
    }
}
