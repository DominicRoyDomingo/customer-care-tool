<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesExclusive extends Model
{
    protected $table = 'services_exclusive';

    protected $guarded = [];

    public function service_type_item() {
        return $this->hasOne(ServiceType::class, 'id', 'service_type')->latest();
    }
}
