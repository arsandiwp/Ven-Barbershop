<?php

namespace App\Services;

use App\Models\Privilege as PrivilegeModel;

class PrivilegeService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;
    private $privilegeModel;

    public function __construct(PrivilegeModel $privilegeModel)
    {
        $this->privilegeModel = $privilegeModel;
    }

    public function getPaginate($per_page, $keyword)
    {
        $container = $this->privilegeModel;
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('name', "like", "%" . $keyword . "%");
                $q->orWhere('description', "like", "%" . $keyword . "%");
            });
        }
        return $container->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }
}
