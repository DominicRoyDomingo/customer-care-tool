<?php

namespace App\Models\MedicalStuff\Traits;

/**
 * Terminology Traits
 */
trait ArticleAttributeTrait
{
    public function getItalianLinkAttribute()
    {
        return string_to_value('it', $this->link_url);
    }

    public function getEnglishLinkAttribute()
    {
        return string_to_value('en', $this->link_url);
    }

    public function getGermanLinkAttribute()
    {
        return string_to_value('de', $this->link_url);
    }

    public function getBisayaLinkAttribute()
    {
        return string_to_value('ph-bis', $this->link_url);
    }

    public function getFilipinoLinkAttribute()
    {
        return string_to_value('ph-fil', $this->link_url);
    }
}
