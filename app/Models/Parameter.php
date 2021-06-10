<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use Searchable;

    protected $table = 'parameter';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        
        static::saved(function ($model) {
            $model->provider_services->filter(function ($item) {
                return Provider::find($item->providers)->searchable();
            })->searchable();
        });
    }

    public function provider_services() {
        return $this->hasMany(ProviderService::class, 'parameter', 'id');
    }
}
