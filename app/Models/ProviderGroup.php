<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderGroup extends Model
{
    protected $table = 'provider_groups';

    protected $guarded = [];

    protected $appends = [
        'provider_group_name',
        'has_translation',
    ];

    public function providers() {
        return $this->hasMany(Provider::class, 'group_id', 'id');
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

    public function getProviderGroupNameAttribute()
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
            }
        }
        
        $name = $mesage;
      }
      return $name;
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
