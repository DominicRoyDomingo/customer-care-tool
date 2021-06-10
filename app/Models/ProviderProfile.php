<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    protected $table = 'provider_profile';

    protected $guarded = [];

    public function provider_profile() {
        return $this->hasOne(Profile::class, 'id', 'profile');
    }
}
