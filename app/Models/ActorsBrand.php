<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActorsBrand extends Model
{
    protected $table = 'actors_brand';

    protected $guarded = [];

    public function brand_object() {
        return $this->hasOne(Brand::class, 'id', 'brands');
    }
}
