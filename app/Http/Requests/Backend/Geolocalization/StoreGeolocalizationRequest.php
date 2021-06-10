<?php

namespace App\Http\Requests\Backend\Geolocalization;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidatePlaces;
use App\Rules\ValidatePlace;

class StoreGeolocalizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $lang =$this->request->get('lang');
        $id = $this->request->get('id') != null ? $this->request->get('id') : "";
        $validations = [
            "places"    => [
                "required", 
                new ValidatePlaces($this->request->get('article_id'),$this->request->get('area'))
            ],
        ];

        if($id != "") {
            $validations = [
                "place"    => [
                    "required", 
                    new ValidatePlace($this->request->get('article_id'),$this->request->get('area'),$id)
                ],
            ];
        }
        $validations['area'] = 'required'; 

        return $validations;
    }

                    
    public function messages()
    {
        return [
            'places.required'  => 'Places is required.',
            'place.required'  => 'Place is required.',
        ];
    }
}
