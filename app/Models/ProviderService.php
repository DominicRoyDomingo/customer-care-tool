<?php

namespace App\Models;

// use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;
use App\Models\Auth\User;

class ProviderService extends Model
{   
    // use Searchable;

    protected $table = 'provider_services';

    protected $guarded = [];

    protected $appends = ['parameter_name', 'provider_and_its_type'];

    // public function toSearchableArray()
    // {
    //     $array = $this->toArray();

    //     $array = $this->transform($array);

    //     $array['post_type']         = 'provider_services';
    //     $array['providers']         =  json_encode([
    //                                     "id"    => $this->provider_item->id,
    //                                     "name"  => $this->provider_item->name,
    //                                 ]);
    //     $array['services']          =  json_encode([
    //                                     "id"    => $this->service_item->id,
    //                                     "name"  => $this->service_item->name,
    //                                 ]);
    //     $array['created_by']        =  "{$this->created_by_item->last_name} {$this->created_by_item->first_name}";
    //     $array['last_modified_by']  =  "{$this->last_modified_by_item->last_name} {$this->last_modified_by_item->first_name}";


    //     return $array;
    // } 

    // public static function boot()
    // {
    //     parent::boot();
        
    //     static::saved(function ($model) {
    //         $model->provider_item->searchable();
    //     });

    //     static::deleted(function($model) {
    //         $model->provider_item->searchable();
    //     });
    // }

    public function provider_item() {
        return $this->hasOne(Provider::class, 'id', 'providers')->latest();
    }

    public function service_item() {
        return $this->hasOne(MedicalTerm::class, 'id', 'services')->latest();
    }

    public function parameter_item() {
        return $this->hasOne(Parameter::class, 'id', 'parameter')->latest();
    }

    public function created_by_item() {
        return $this->hasOne(User::class, 'id', 'created_by')->latest();
    }

    public function last_modified_by_item() {
        return $this->hasOne(User::class, 'id', 'last_modified_by')->latest();
    }

    public function getParameterNameAttribute()
    {

        return $this->parameter_item == null ? null : $this->parameter_item->name;

    }

    public function getProviderAndItsTypeAttribute()
    {

        return $this->provider_item->provider_and_its_type_items;

    }
}
