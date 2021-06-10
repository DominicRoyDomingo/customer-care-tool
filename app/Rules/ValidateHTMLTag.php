<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateHTMLTag implements Rule
{
    public $tags;
    public $placeHolders;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($tags, $placeHolders)
    {   
        $this->tags = $tags;
        $this->placeHolders = $placeHolders;
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
        foreach ($value as $key => $image) {
            if($image == "null" && $this->placeHolders[$key] == "null") return false;
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
        return 'HTML Tag should have an image.';
    }
}
