<?php

namespace Modules\Category\Database\Repositories\Api\V1;

use Modules\Category\Entities\Category;

class CtegoryRepo
{
    public $query;

    public function __construct()
    {
        $this->query = Category::query();
    }

    public function paginate($perPage = 10)
    {
        return $this->query->paginate($perPage);
    }

    public function findById($id)
    {
        return Category::query()->findOrFail($id);
    }

    public function store($values)
    {
        return Category::create([
            'parent_id' => $values->parent_id,
            'name' => $values->name,
            'description' => $values->description
        ]);
    }

    public function update($id, $values)
    {
        $brand = $this->findById($id);
        return $brand->update([
            'parent_id' => $values->parent_id,
            'name' => $values->name,
            'description' => $values->description
        ]);
    }
}
