<?php

namespace App\Models\Questions\Choice\Traits\Attributes;

/**
 * Choice Attribute
 */
trait ChoiceAttribute
{

    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getChoiceNameAttribute()
    {
        return $this->get_base_name(true);
    }

    /**
     * @return String
     */
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->value);
    }

    /**
     * @return String
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->value);
    }

    /**
     * @return String
     */
    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->value);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->value);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->value);
    }

    public function getHasTranslationAttribute()
    {
       $baseName = $this->base_name();
 
       if (!$baseName) {
          return "";
       }
 
       return $baseName ;
    }

    public function getOnSelectChoiceNameAttribute()
    {
      return $this->choice_name;
    }
}
