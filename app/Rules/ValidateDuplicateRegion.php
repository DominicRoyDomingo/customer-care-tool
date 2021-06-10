<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Region;

class ValidateDuplicateRegion implements Rule
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
        $region = Region::where('country', $this->countryId)->where('region', $value)->first();

        if($region != null) {
            if($this->id == null) {
                $this->placeError = $value;
                return false;
            } else if($region->id != $this->id) {
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
        return $this->placeError.' is an existing region record.';
    }
}
