<?php

namespace App\Models\Algolia;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class AlgoliaPermission extends Model
{
    protected $table = 'algolia_permission';

    protected $guarded = [];

    public function brand_item() 
    {
        return $this->hasOne(Brand::class, 'id', 'brand');
    }

    public function class_item() 
    {
        return $this->hasOne(AlgoliaClass::class, 'id', 'class');
    }
}
