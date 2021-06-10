<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesBrand extends Model
{
    protected $table = 'services_brand';

    protected $guarded = [];

    public function brand_object() {
        return $this->hasOne(Brand::class, 'id', 'brands');
    }
}
