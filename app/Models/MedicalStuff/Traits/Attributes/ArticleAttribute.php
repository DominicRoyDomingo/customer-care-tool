<?php

namespace App\Models\MedicalStuff\Traits\Attributes;


/**
 * Article Attributes List
 */
trait ArticleAttribute
{

    /**
     * @return String
     */
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->title);
    }

    /**
     * @return String
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->title);
    }

    /**
     * @return String
     */
    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->title);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->title);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->title);
    }
}
