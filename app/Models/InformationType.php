<?php

namespace App\Models;

use App\Models\Traits\BaseName;
use Illuminate\Database\Eloquent\Model;

class InformationType extends Model
{
    use BaseName;
    protected $table = 'information_type';
    protected $guarded = [];

    protected $appends = [
        'information_type_name',
        'has_translation',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'base_name',
    ];

    public function getIsLoadingAttribute()
    {
        return false;
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

    public function getInformationTypeNameAttribute()
    {
        return $this->get_base_name(true);
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
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
