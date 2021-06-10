<?php

namespace App\Models\MedicalStuff;

use App\Models\MedicalStuff\Traits\TermTraits;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\TermDescription;

class TermConnectionDescription extends Model
{
    use TermTraits;

    protected $table = 'term_connection_descriptions';

    protected $guarded = [];

    protected $appends = [
        'note_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'base_name',
        'on_select_note_name',
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

    public function getNoteNameAttribute()
    {
        if (!$this->base_name) {
            return null;
        }

        return $this->get_base_name(true);
    }

    /**
     * @return bool
     */
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getOnSelectNoteNameAttribute()
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
        return string_to_value('en', $this->note);
    }

    /**
     * @return String
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->note);
    }

    /**
     * @return String
     */
    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->note);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->note);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->note);
    }

    public function term_description()
    {
        return $this->belongsTo(TermDescription::class, 'description_id');
    }

    public function term()
    {
        return $this->belongsTo(MedicalTerm::class, 'term_id');
    }
}
