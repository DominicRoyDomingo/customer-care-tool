<?php

namespace App\Models\Questions\Traits\Attributes;

/**
 * Qeustion Type Attribute
 */
trait QuestionTypeAttribute
{
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getQuestionTypeNameAttribute()
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

    public function getOnSelectQuestionTypeNameAttribute()
    {
      return $this->question_type_name;
    }

   /**
     * Get name attribute
     * @return array
     */
    public function getTranslatedNameAttribute()
    {
        // $locale = locale() ?? "en";
        $names = $this->name;

        $arrNames = [];

        if (!is_null($names) || $names != "") {
            $parsedNames = json_decode($names);
            foreach ($parsedNames as $k => $v) {
                $arrNames[$k] = $v;
            }
        }
        return $arrNames;
    }

    /**
     * Get current translated name based on current locale
     * @return string
     */
    public function getLocaleNameAttribute()
    { 
        $lang = locale() ?? "en";
        return getTranslation($this->name, $lang);
    }
}
