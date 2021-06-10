<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ArticleContentMaker\Geolocalization;

class ValidatePlace implements Rule
{
    public $placeError;
    public $articleId;
    public $area;
    public $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($articleId,$area,$id)
    {
        $this->articleId = $articleId;
        $this->area = $area;
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
        $place = json_decode($value);
        if($this->area == 'City') {
            $geolocalization = Geolocalization::where('article', $this->articleId)->where('country', $place->country_id)->where('city', $place->city_id)->where('division', $place->division_id)->first();
        }

        if($this->area == 'Province') {
            $geolocalization = Geolocalization::where('article', $this->articleId)->where('country', $place->country_id)->where('city', null)->where('division', $place->division_id)->first();
        }

        if($this->area == 'Region') {
            $geolocalization = Geolocalization::where('article', $this->articleId)->where('country', $place->country_id)->where('regions', $place->region_id)->first();
        }

        if($geolocalization != null) {
            if($geolocalization->id != $this->id) {
                $this->placeError = $place;
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
        return $this->placeError->place.' is an existing geolocalization record.';
    }
}
