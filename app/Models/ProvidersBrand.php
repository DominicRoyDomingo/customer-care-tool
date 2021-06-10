<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvidersBrand extends Model
{
    
    protected $table = 'providers_brand';

    protected $guarded = [];

    public function brand_object() {
        return $this->hasOne(Brand::class, 'id', 'brands');
    }
}
