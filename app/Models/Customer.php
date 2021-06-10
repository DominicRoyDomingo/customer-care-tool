<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table = 'customers';
    protected $guarded = [];

    public function profile() {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
