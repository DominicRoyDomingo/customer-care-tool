<?php

namespace App\Models\ArticleContentMaker;

use Illuminate\Database\Eloquent\Model;

class GeolocalizationTemplate extends Model
{
    protected $table = 'geolocalization_template';

    protected $guarded = [];

    public function country_item() {
        return $this->hasOne(Country::class, 'id', 'country');
    }

    public function division_item() {
        return $this->hasOne(Division::class, 'id', 'division');
    }

    public function city_item() {
        return $this->hasOne(City::class, 'id', 'city');
    }
}
