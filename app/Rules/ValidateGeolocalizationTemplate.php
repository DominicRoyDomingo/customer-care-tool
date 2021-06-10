<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MedicalStuff\PublishingItemContent;

class ValidateGeolocalizationTemplate implements Rule
{
    public $articleId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $geolocalizationTemplate = PublishingItemContent::where('publishing_item', $this->articleId)->first();

        if($geolocalizationTemplate == null) {
            return false;
    }
        
        return true; 
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No Template Exists.';
    }
}
