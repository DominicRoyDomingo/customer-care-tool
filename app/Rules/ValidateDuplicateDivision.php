<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Khsing\World\Models\Division;

class ValidateDuplicateDivision implements Rule
{
    public $countryId;
    public $placeError;
    public $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($countryId, $id = null)
    {
        $this->countryId = $countryId;
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
        $division = Division::where('country_id', $this->countryId)->where('name', $value)->first();

        if($division != null) {
            if($this->id == null) {
                $this->placeError = $value;
                return false;
            } else if($division->id != $this->id) {
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
        return $this->placeError.' is an existing division record.';
    }
}
