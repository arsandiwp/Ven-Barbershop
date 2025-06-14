<?php

namespace App\Services;

use App\Models\TemplateWeb as TemplateWebModel;

class TemplateWebService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;
    private $templateWebModel;

    public function __construct(TemplateWebModel $templateWebModel)
    {
        $this->templateWebModel = $templateWebModel;
    }

    public function getPaginate($per_page, $keyword)
    {
        $container = $this->templateWebModel;
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('question', "like", "%" . $keyword . "%");
            });
        }
        return $container->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }

    public function getAll(){
        return $this->templateWebModel->get();
    }

    public function get($id)
    {
        return $this->templateWebModel::where('id', $id);
    }

    public function create($data)
    {
        return $this->templateWebModel::create($data)->id;
    }

    public function update($id, $data)
    {
        return $this->templateWebModel::where(self::PRIMARY_KEY, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->templateWebModel::where(self::PRIMARY_KEY, $id)->delete();
    }
}
