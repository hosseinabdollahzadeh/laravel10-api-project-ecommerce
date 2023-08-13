<?php

namespace Modules\Brand\Database\Repositories\Api\V1;

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

    public function findById($id)
    {
        return Brand::query()->findOrFail($id);
    }

    public function store($values)
    {
        return Brand::create([
           'name' => $values->name,
           'display_name' => $values->display_name,
        ]);
    }

    public function update($id, $values)
    {
        $brand = $this->findById($id);
        return $brand->update([
            'name' => $values->name,
            'display_name' => $values->display_name,
        ]);
    }
}
