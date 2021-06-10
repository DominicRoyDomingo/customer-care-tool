<?php

namespace App\Models\Questions\Choice\Traits\Attributes;

/**
 * Questionnaire Attribute
 */
trait QuestionAttribute
{
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getQuestionNameAttribute()
    {
        return $this->get_base_name(true);
    }

    /**
     * @return String
     */
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->question);
    }

    /**
     * @return String
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->question);
    }

    /**
     * @return String
     */
    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->question);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->question);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->question);
    }

    public function getTranslatedQuestionAttribute()
    {
        // $locale = locale() ?? "en";
        $questions = $this->question;

        $arrQuestions = [];

        if (!is_null($questions) || $questions != "") {
            $parsedNames = json_decode($questions);
            foreach ($parsedNames as $k => $v) {
                $arrQuestions[$k] = $v;
            }
        }
        
        return $arrQuestions;
    }

    /**
     * Get current translated question based on current locale
     * @return string
     */
    public function getLocaleQuestionAttribute()
    {
        $locale = locale() ?? "en";
        return getTranslation($this->question, $locale);
    }
}
