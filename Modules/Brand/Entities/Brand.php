<?php

namespace Modules\Brand\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Entities\Product;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'brands';
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
