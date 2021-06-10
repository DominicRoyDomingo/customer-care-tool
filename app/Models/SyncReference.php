<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncReference extends Model
{
    protected $table = 'sync_reference';

    protected $guarded = [];

    public function provider() {
        return $this->hasOne(Provider::class, 'id', 'table_id');
    }
}
