<?php

namespace App\Services;

use App\Models\Qna as QnaModel;

class QnaService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;
    private $qnaModel;

    public function __construct(QnaModel $qnaModel)
    {
        $this->qnaModel = $qnaModel;
    }

    public function getPaginate($per_page, $keyword)
    {
        $container = $this->qnaModel;
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('question', "like", "%" . $keyword . "%");
            });
        }
        return $container->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }

    public function get($id)
    {
        return $this->qnaModel::where('id', $id);
    }

    public function create($data)
    {
        return $this->qnaModel::create($data)->id;
    }

    public function update($id, $data)
    {
        return $this->qnaModel::where(self::PRIMARY_KEY, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->qnaModel::where(self::PRIMARY_KEY, $id)->delete();
    }
}
