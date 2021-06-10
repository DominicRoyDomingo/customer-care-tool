<?php

namespace App\Models\MedicalStuff;

use App\Models\MedicalStuff\Traits\TermTraits;
use Illuminate\Database\Eloquent\Model;

class TypeDate extends Model
{
    use TermTraits;

    protected $guarded = [];

    protected $appends = [
        'type_date_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'base_name',
        'has_date',
        'on_select_type_date_name',
        'base_language',
        'is_bisaya',
        'is_filipino',
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

        if ($this->is_bisaya) {
            return 'ph-bis';
        }

        if ($this->is_filipino) {
            return 'ph-fil';
        }
    }

    public function getLocaleAttribute()
    {
        return request()->locale;
    }

    /**
     * @return bool
     */
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getHasDateAttribute()
    {
        if (!$this->pivot) {
            return null;
        }

        return date('F d, Y', strtotime($this->pivot->article_date));
    }

    public function getCreatedDate()
    {
        if (!$this->pivot) {
            return null;
        }

        return date('F d, Y', strtotime($this->pivot->created_at));
    }

    public function getUpdatedDate()
    {
        if (!$this->pivot) {
            return null;
        }

        return date('F d, Y', strtotime($this->pivot->updated_at));
    }

    public function getOnSelectTypeDateNameAttribute()
    {
        return $this->type_date_name;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getTypeDateNameAttribute()
    {
        return $this->get_base_name(true);
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

    public function articles()
    {
        return $this->belongsToMany(MedicalArticle::class, 'article_dates', 'article_id', 'type_date_id')
            ->withPivot('article_date');
    }
}
