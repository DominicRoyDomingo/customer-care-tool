<?php

namespace App\Models;

// use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class ColorCoding extends Model
{
    // use Searchable;

    protected $table = 'color_coding';

    protected $guarded = [];

    // public function reasons_request_type() {
    //     return $this->hasMany(RequestType::class, 'id', 'request_type')->latest();
    // }

}
