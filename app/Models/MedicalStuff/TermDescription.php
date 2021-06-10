<?php

namespace App\Models\MedicalStuff;

use App\Models\MedicalStuff\Traits\TermTraits;
use DB;
use Illuminate\Database\Eloquent\Model;

class TermDescription extends Model
{
    use TermTraits;

    protected $table = 'connection_descriptions';
    // protected $table = 'term_connection_descriptions';

    protected $guarded = [];

    protected $appends = [
        'description_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'base_name',
        'on_select_description_name',
        'base_language',
    ];

    public function getBaseLanguageAttribute()
    {
        if ($this->is_english) {
            return 'en';
        }

        if ($this->is_italian) {
            return 'it';
        }

        if ($this->is_german) {
            return 'de';
        }

        if ($this->is_filipino) {
            return 'de';
        }

        if ($this->is_bisaya) {
            return 'de';
        }
    }

    public function getDescriptionNameAttribute()
    {
        return $this->get_base_name(true);
    }

    /**
     * @return bool
     */
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getOnSelectDescriptionNameAttribute()
    {
        return $this->description_name;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    /**
     * @return String
     */
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->name);
    }

    /**
     * @return String
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->name);
    }

    /**
     * @return String
     */
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

    public function term_connections_descriptions()
    {
        return $this->hasOne(TermConnectionDescription::class, 'description_id');
    }
}
