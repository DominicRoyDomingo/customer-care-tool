<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Khsing\World\Models\City;

class ValidateDuplicateCity implements Rule
{
    public $countryId;
    public $divisionId;
    public $placeError;
    public $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($countryId, $divisionId, $id = null)
    {
        $this->countryId = $countryId;
        $this->divisionId = $divisionId;
        $this->id = $id;

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
   
        $city = City::where('country_id', $this->countryId)->where('division_id', $this->divisionId)->where('name', $value)->first();

        if($city != null) {
            if($this->id == null) {
                $this->placeError = $value;
                return false;
            } else if($city->id != $this->id) {
                $this->placeError = $value;
                return false;
            }
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
        return $this->placeError.' is an existing city record.';
    }
}
