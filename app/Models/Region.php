<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Khsing\World\Models\Country;

class Region extends Model
{
    protected $table = 'world_regions';

    protected $guarded = [];

    public function country() {
        return $this->belongsTo(Country::class, 'country');
    }
}
