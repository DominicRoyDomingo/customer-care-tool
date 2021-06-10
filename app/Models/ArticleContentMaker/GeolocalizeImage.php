<?php

namespace App\Models\ArticleContentMaker;

use Illuminate\Database\Eloquent\Model;

class GeolocalizeImage extends Model
{
    protected $table = 'geolocalize_images';

    protected $guarded = [];

    public function geolocalization() 
    {
        return $this->belongsTo(Geolocalization::class, 'place');
    }
}
