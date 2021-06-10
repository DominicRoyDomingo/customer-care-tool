<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MedicalStuff\Icon;

class ValidateTermProviderType implements Rule
{
    public $term;
    public $providerType;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($term)
    {
        $this->term = $term;
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
        $term = Icon::where('term', $this->term)->where('provider_type', $value)->first();
        
        if($term != null) {
            $this->providerType = $term->medical_term_provider_type->base_name;
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
        return 'There is an existing icon for the '. $this->providerType . '.';
    }
}
