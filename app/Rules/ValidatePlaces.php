<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ArticleContentMaker\Geolocalization;

class ValidatePlaces implements Rule
{
    public $placeError;
    public $articleId;
    public $area;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($articleId, $area)
    {
        $this->articleId = $articleId;
        $this->area = $area;
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
        $places = json_decode($value);
        if($this->area == 'City') {
            foreach($places as $place) {
                $geolocalization = Geolocalization::where('article', $this->articleId)->where('country', $place->country_id)->where('city', $place->city_id)->where('division', $place->division_id)->first();
                if($geolocalization != null) {
                    $this->placeError = $place;
                    return false;
                }
            }
        }

        if($this->area == 'Province') {
            foreach($places as $place) {
                $geolocalization = Geolocalization::where('article', $this->articleId)->where('country', $place->country_id)->where('city', null)->where('division', $place->division_id)->first();
                if($geolocalization != null) {
                    $this->placeError = $place;
                    return false;
                }
            }
        }

        if($this->area == 'Region') {
            foreach($places as $place) {
                $geolocalization = Geolocalization::where('article', $this->articleId)->where('country', $place->country_id)->where('regions', $place->region_id)->first();
                if($geolocalization != null) {
                    $this->placeError = $place;
                    return false;
                }
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
