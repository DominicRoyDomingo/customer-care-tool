<?php

namespace App\Models\MedicalStuff\Traits\Attributes;

use App\Models\Jobs\JobDescription;
use App\Models\MedicalStuff\MedicalTermType;
use App\Models\MedicalStuff\TermConnectionDescription;

/**
 * Terminology Traits
 */
trait TermAttribute
{
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

    public function getRouteShowAttribute()
    {
        return route("admin.categorized-terms.terminology-show", $this->id);
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

    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getHasSpecializationsAttribute()
    {
        if (!$this->specializations) {
            return null;
        }

        if (!string_to_value('specializations', $this->specializations)) {
            return null;
        }

        $items = [];

        foreach (string_to_value('specializations', $this->specializations) as $id) {

            $jd = JobDescription::findOrFail($id);

            if ($jd) {
                $items[] = $jd;
            }
        }

        return $items;
    }



    public function base_note_name($key = false)
    {

        if (!$this->note) {
            return null;
        }

        $isEnglish = null;
        $isItalian = null;
        $isGerman = null;
        $isBisaya = null;
        $isFilipino = null;

        switch (locale()) {
            case 'en':
                $isEnglish = string_to_value(locale(), $this->note);
                break;
            case 'it':
                $isItalian = string_to_value(locale(), $this->note);
                break;
            case 'de':
                $isGerman = string_to_value(locale(), $this->note);
                break;
            case 'ph-bis':
                $isBisaya = string_to_value(locale(), $this->note);
                break;
            case 'ph-fil':
                $isFilipino = string_to_value(locale(), $this->note);
                break;
        }

        $baseName = $isEnglish;
        if (locale() === 'it')  $baseName = $isItalian;
        if (locale() === 'de')  $baseName = $isGerman;
        if (locale() === 'ph-fil')  $baseName = $isBisaya;
        if (locale() === 'ph-bis')  $baseName = $isFilipino;


        if (!$baseName) {
            $priorityBaseName = $isEnglish ?? $isItalian;
            $priorityBaseName = $priorityBaseName ?? $isGerman;
            $priorityBaseName = $priorityBaseName ?? $isFilipino;
            $baseName .= $priorityBaseName ?? $isBisaya;

            if ($key) {
                $language = language();
                $baseName .= "<i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
            }
        }

        return   $baseName;
    }
}
