<?php

namespace App\Models;

// use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $table = 'request_type';

    protected $guarded = [];

    // use Searchable;

    // public static function boot()
    // {
    //     parent::boot();
        
    //     static::saved(function ($model) {
    //         $model->reasons->filter(function ($item) {
    //             return $item->provider_services->filter(function ($item) {
    //                 return Provider::find($item->providers)->searchable();
    //             })->searchable();
    //         })->searchable();
    //     });
    // }

    public function reasons() {
        return $this->hasMany(Reasons::class, 'request_type', 'id');
    }

    // public function brand() {
    //     return $this->hasMany(Brand::class, 'brands', 'id');
    // }
}
