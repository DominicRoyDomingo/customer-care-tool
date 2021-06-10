<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandClient extends Model
{
    //
    protected $guarded = [];

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function profile() {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

}
