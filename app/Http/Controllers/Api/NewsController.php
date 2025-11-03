<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\NewsService;
use Schema;
use DB;
use App\Exceptions\CustomException;
use App\Helpers\ResponseHelper;
use App\Models\NewsImage;
use App\Services\FileHandlerService;

class NewsController extends Controller
{
    private $mainService;
    private $fileHandlerService;

    public function __construct(NewsService $mainService, FileHandlerService $fileHandlerService)
    {
        $this->mainService = $mainService;
        $this->fileHandlerService = $fileHandlerService;
    }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $filter = [];        

        if ($request->status) {
            $filter['status'] = $request->status;
            if ($filter['status'] == "All")
                $filter['status'] = null;
        } else {
            $filter['status'] = "Published";
        }

        return $this->mainService->getPaginate($per_page, $keyword, $filter);
    }

    public function getDetail($identifier)
    {
        if (is_numeric($identifier)) {
            $result = $this->mainService->find($identifier);
        } else {
            $result = $this->mainService->getCustomUrl($identifier);
        }

        return $result;
    }

    public function create(Request $request)
    {        
        $data = $request->only(Schema::getColumnListing('news'));        
        if ($data['custom_url'] == '') {
            $data['custom_url'] = null;
        } else {
            $customUrlExist = $this->mainService->getCustomUrl($data['custom_url']);
            if ($customUrlExist) {
                throw new CustomException("Requested Custom URL already exist! Please use another one!");
            }
        }

        return DB::transaction(function () use ($data, $request) {            
            if ($data['image_url'] == '') {
                $data['image_url'] = null;
            }            
            $queryResult = $this->mainService->create($data);
            $newlyNews = $this->mainService->find($queryResult);
            
            if ($request->file('main_file') != null) {
                $image = $request->file('main_file');
                $file_path = "/news/" . $queryResult . "/image";

                $image_url = (string) $this->fileHandlerService->saveFileToStorage($image, $file_path);

                $newlyNews->update([
                    'image_url' => $image_url,
                ]);
            }
            
            if ($request->arr_images) {
                $arr_images = $request->arr_images;
                
                foreach ($arr_images as $img) {
                    if (\key_exists('file', $img) && !!$img['file']) {
                        if (is_file($img['file'])) {
                            $image = $img['file'];
                            $images_file_path = "/news/" . $queryResult . "/images";

                            $img['image_url'] = (string) $this->fileHandlerService->saveFileToStorage($image, $images_file_path);

                            if ($img['image_url']) {
                                $newImg = ['image_url' => $img['image_url']];
                                $newlyNews->images()->create($newImg);
                            }
                        }
                    }
                }
            }

            if ($queryResult) {
                return ResponseHelper::create($queryResult);
            }

            throw new CustomException("Error creating News!");
        });
    }

    public function update($id, Request $request)
    {        

        $data = $request->only(Schema::getColumnListing('news'));        
        if ($data['custom_url'] == '') {
            $data['custom_url'] = null;
        } else {
            $customUrlExist = $this->mainService->getCustomUrl($data['custom_url']);
            if ($customUrlExist && $customUrlExist->id != $id) {
                throw new CustomException("Requested Custom URL already exist! Please use another one!");
            }
        }

        $exist = $this->mainService->find($id);

        return DB::transaction(function () use ($id, $data, $exist, $request) {
            if ($request->arr_images) {
                $arr_images = $request->arr_images;
                $keep_ids = collect($request->arr_images)->pluck('id')->unique();
                $query = NewsImage::where('news_id', $id)->whereNotIn('id', $keep_ids);                
                $removedImages = $query->get();
                foreach ($removedImages as $img) {
                    if (!empty($img->image_url)) {
                        $pathArr = explode(("news/" . $exist->id . "/images/"), $img->image_url);

                        if ($pathArr && count($pathArr) > 1) {
                            $name = $pathArr[1];
                            $file_path = "public/news/" . $exist->id . "/images/" . $name;
                            $this->fileHandlerService->deleteFileFromStorage($file_path);
                        }
                    }
                }

                $query->delete();
                
                foreach ($arr_images as $img) {
                    if (\key_exists('file', $img) && !!$img['file']) {
                        if (is_file($img['file'])) {
                            $image = $img['file'];                            
                            $images_file_path = "/news/" . $exist->id . "/images";

                            $img['image_url'] = (string) $this->fileHandlerService->saveFileToStorage($image, $images_file_path);

                            if ($img['image_url']) {
                                $newImg = ['image_url' => $img['image_url']];
                                $exist->images()->create($newImg);
                            }
                        }
                    }
                }
            }

            if ($request->file('main_file') != null) {
                if ($exist && !empty($exist->image_url)) {
                    $file_path = "public/news/" . $exist->id . "/image";
                    $this->fileHandlerService->deleteFileItemDirectory($file_path);
                }

                $image = $request->file('main_file');
                $file_path = "/news/" . $exist->id . "/image";

                $data['image_url'] = (string) $this->fileHandlerService->saveFileToStorage($image, $file_path);
            }

            if ($data['image_url'] == '') {
                $data['image_url'] = null;
            }

            $this->mainService->update($id, $data);

            return ResponseHelper::put();
        });
    }

    public function delete($id, Request $request)
    {
        $exist = $this->mainService->find($id);
        if (!$exist) {
            throw new CustomException("Requested News doesn't exist or no longer available!");
        }

        return DB::transaction(function () use ($id) {
            $this->mainService->delete($id);

            return ResponseHelper::delete();
        });
    }
}
