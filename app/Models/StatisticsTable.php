<?php

namespace App\Models;

use App\Models\MedicalStuff\Traits\Attributes\TermAttribute;
use App\Models\MedicalStuff\Traits\TermTraits;
use Illuminate\Database\Eloquent\Model;

class StatisticsTable extends Model
{
    use TermTraits, TermAttribute;

    protected $guarded = [];

    protected $appends = [
        'is_loading',
        'base_name',
        'display_name',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'base_language'
    ];


    public function fields()
    {
        return $this->hasMany(StatisticsField::class, 'statistics_table_id');
    }


    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getDisplayNameAttribute()
    {
        return $this->get_base_name(true);
    }

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
            return 'ph-fil';
        }

        if ($this->is_bisaya) {
            return 'ph-bis';
        }
    }

    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->display);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->display);
    }

    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->display);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->display);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->display);
    }

    // public function getDisplayNameAttribute()
    // {
    //     $name = string_to_value(locale(), $this->display);

    //     $base_name = $this->getIsEnglishAttribute();

    //     if (is_null($name)) {

    //         $mesage =  ' <small style="color:red">(No English translation yet)</small>';

    //         switch (locale()) {
    //             case 'it':
    //                 $mesage = $base_name . ' <small style="color:red">(No Italian translation yet)</small>';
    //                 break;
    //             case 'de':
    //                 $mesage = $base_name . ' <small style="color:red">(No German translation yet)</small>';
    //                 break;
    //         }

    //         $name = $mesage;
    //     }

    //     return $name;
    // }

    public function getIsLoadingAttribute()
    {
        return false;
    }
}
