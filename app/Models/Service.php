<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;

class Service extends Model
{
    use Searchable;

    protected $table = 'services';

    protected $appends = [
        'selected_brands',
        'service_name',
        'has_translation'
    ];

    protected $guarded = [];

    public function services_brand_item() {
        return $this->hasOne(ServicesBrand::class, 'service', 'id');
    }

    public function getSelectedBrandsAttribute() {

        $brands = ServicesBrand::where('service', $this->id)->get();
        $brandsArr = array();
        foreach ($brands as $brand ) {
            array_push( $brandsArr, $brand->brands );
        }
        return $brandsArr;
    }

    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->name);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->name);
    }

    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->name);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->name);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->name);
    }

    public function getServiceNameAttribute()
    {
      $name = string_to_value(locale(), $this->name);

      $base_name = $this->getIsEnglishAttribute();

      if (is_null($name)) {

        $new = '';
        $mesage = $this->name;
        if(json_decode($this->name) != null) {
            $availableLanguage = array_key_first(json_decode($this->name, true));
            $new = json_decode($this->name)->$availableLanguage;
            $mesage =  $new . ' (No English translation yet)';

            switch (locale()) {
            case 'it':
                $mesage = $new . ' (No Italian translation yet)';
                break;
            case 'de':
                $mesage = $new . ' (No German translation yet)';
                break;
            case 'ph-fil':
                $mesage = $new . ' (No Filipino translation yet)';
                break;
            case 'ph-bis':
                $mesage = $new . ' (No Visayan translation yet)';
                break;    
            }
        }
        

        $name = $mesage;
      }
      return $name;
    }
    
    public static function boot()
    {
        parent::boot();
        
        static::saved(function ($model) {
            $model->provider_services->filter(function ($item) {
                return Provider::find($item->providers)->searchable();
            })->searchable();
        });
    }

    public function service_type_item() {
        return $this->hasOne(ServiceType::class, 'id', 'service_type')->latest();
    }

    public function provider_services() {
        return $this->hasMany(ProviderService::class, 'services', 'id');
    }

    public function medical_term_item() {
        return $this->hasOne(MedicalTerm::class, 'id', 'medical_term')->latest();
    }

    public function base_name()
    {
        $baseName = $this->is_english;
        if (locale() === 'it')  $baseName = $this->is_italian;
        if (locale() === 'de')  $baseName = $this->is_german;
        if (locale() === 'ph-fil')  $baseName = $this->is_filipino;
        if (locale() === 'ph-bis')  $baseName = $this->is_bisaya;

        return $baseName;
    }


    public function getHasTranslationAttribute()
    {
        $baseName = $this->base_name();

        if (!$baseName) {
            return false;
        }

        return true;
    }
}
