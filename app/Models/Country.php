<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'world_countries';

    protected $guarded = [];

    public function divisions() {
        return $this->hasMany(Division::class, 'country_id', 'id');
    }

    public function cities() {
        return $this->hasMany(City::class, 'country_id', 'id');
    }

    public function regions() {
        return $this->hasMany(Region::class, 'country', 'id');
    }
}
