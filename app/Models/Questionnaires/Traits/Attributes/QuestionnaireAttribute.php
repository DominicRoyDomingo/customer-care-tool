<?php

namespace App\Models\Questionnaires\Traits\Attributes;

/**
 * Questionnaire Attribute
 */
trait QuestionnaireAttribute
{
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getQuestionnaireNameAttribute()
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
}
