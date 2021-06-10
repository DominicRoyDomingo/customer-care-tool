<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Algolia\AlgoliaPermission;

class ValidateBrandAndClassDuplicate implements Rule
{
    private $class;
    private $algoliaPermissionID;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($class, $algoliaPermissionID)
    {
        $this->class = $class;
        $this->algoliaPermissionID = $algoliaPermissionID;
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
        $algoliaPermission = AlgoliaPermission::where('class', $this->class)->where('brand', $value)->first();

        if($algoliaPermission != null) {
            if($algoliaPermission->id != $this->algoliaPermissionID) {
                return false;   
            }
        };

        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'algolia_permission_existing';
    }
}
