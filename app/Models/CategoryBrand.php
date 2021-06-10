<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBrand extends Model
{
    protected $table = 'category_of_brand';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
