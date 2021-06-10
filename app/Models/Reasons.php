<?php

namespace App\Models;

// use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Reasons extends Model
{
    // use Searchable;

    protected $table = 'reasons';

    protected $guarded = [];

    public function reasons_request_type() {
        return $this->hasMany(RequestType::class, 'id', 'request_type')->latest();
    }

}
