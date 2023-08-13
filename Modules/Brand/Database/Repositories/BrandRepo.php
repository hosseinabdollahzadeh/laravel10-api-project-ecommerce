<?php

namespace Modules\Brand\Database\Repositories;

use Modules\Brand\Entities\Brand;

class BrandRepo
{
    public $query;

    public function __construct()
    {
        $this->query = Brand::query();
    }

    public function paginate($perPage = 10)
    {
        return $this->query->paginate($perPage);
    }

    public function store($values)
    {
        return Brand::create([
           'name' => $values->name,
           'display_name' => $values->display_name,
        ]);
    }
}
