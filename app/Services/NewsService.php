<?php

namespace App\Services;

use App\Models\News;

class NewsService
{
    const DEFAULT_PER_PAGE = 6;
    private $model;

    public function __construct(News $news)
    {
        $this->model = $news;
    }

    public function find($id)
    {
        return $this->model::with(['images'])->find($id);
    }

    public function getCustomUrl($custom_url)
    {
        return $this->model::with(['images'])->where('custom_url', $custom_url)->first();
    }

    public function getPaginate($per_page, $keyword, $filter = [])
    {
        $container = $this->model;
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('title', "like", "%" . $keyword . "%");
                $q->orWhere('description', "like", "%" . $keyword . "%");
            });
        }

        if (count($filter)) {
            $container = $container->where(function ($q) use ($filter) {
                foreach ($filter as $field => $value) {
                    if ($value && !empty($value)) {
                        $q->where($field, $value);
                    }
                }

            });
        }

        $container = $container->with(['user']);

        return $container->orderBy('date', 'desc')->orderBy('updated_at', 'desc')->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }

    public function delete($id)
    {
        return $this->model::where('id', $id)->delete();
    }

    public function update($id, $data)
    {
        return $this->model::where('id', $id)->update($data);
    }

    public function create($data)
    {
        return $this->model::create($data)->id;
    }
}
