<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ArticleContentMaker\Geolocalization;

class ValidateGeolocalizationImage implements Rule
{
    public $geolocalizationId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($geolocalizationId)
    {
        $this->geolocalizationId = $geolocalizationId;
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
        $geolocalization = Geolocalization::where('id', $this->geolocalizationId)->first();
        if($geolocalization->geolocalize_images->isEmpty()) {
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
        return 'No Images Exists.';
    }
}
