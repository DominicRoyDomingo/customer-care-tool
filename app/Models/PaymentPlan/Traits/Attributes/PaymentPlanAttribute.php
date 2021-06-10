<?php

namespace App\Models\PaymentPlan\Traits\Attributes;

/**
 * Choice Attribute
 */
trait PaymentPlanAttribute
{

    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getPaymentPlanNameAttribute()
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

    public function getHasTranslationAttribute()
    {
       $baseName = $this->base_name();
 
       if (!$baseName) {
          return "";
       }
 
       return $baseName ;
    }

    public function getOnSelectPaymentPlanAttribute()
    {
      return $this->payment_plan_name;
    }

    public function getBaseFeaturesAttribute()
    {
      return json_decode($this->features, true);
    }

    public function getBaseDescriptionAttribute()
    {
      return json_decode($this->description,true);
    }
}
