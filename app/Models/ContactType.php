<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    protected $guarded = [];
    protected $appends = ['is_loading', 'contact_type_name', 'is_english', 'is_italian', 'is_german', 'is_filipino', 'is_visayan'];
        
    public function contacts() {
        return $this->hasMany(Contact::class);
    }
    
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->type_name);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->type_name);
    }

    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->type_name);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->type_name);
    }
    
    public function getIsVisayanAttribute()
    {
        return string_to_value('ph-bis', $this->type_name);
    }
    

    public function getContactTypeNameAttribute()
    {
        $name = string_to_value(locale(), $this->type_name);

        $base_name = $this -> getIsEnglishAttribute();
      
        if (is_null($name)) {

            $mesage =  ' <small style="color:red">(No English translation yet)</small>';

            switch (locale()) {
                case 'it':
                    $mesage = $base_name. ' <small style="color:red">(No Italian translation yet)</small>';
                    break;
                case 'de':
                    $mesage = $base_name. ' <small style="color:red">(No German translation yet)</small>';
                    break;
                case 'ph-fil':
                    $mesage = $base_name. ' <small style="color:red">(No Filipino translation yet)</small>';
                    break;
                case 'ph-bis':
                    $mesage = $base_name. ' <small style="color:red">(No Visayan translation yet)</small>';
                    break;
            }

            $name = $mesage;
        }
        
        return $name;
    }
    public function getIsLoadingAttribute()
    {
        return false;
    }
}
