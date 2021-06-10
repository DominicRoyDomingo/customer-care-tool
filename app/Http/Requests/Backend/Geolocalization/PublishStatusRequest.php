<?php

namespace App\Http\Requests\Backend\Geolocalization;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateGeolocalizationImage;
use App\Rules\ValidateGeolocalizationTemplate;

class PublishStatusRequest extends FormRequest
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
        return [
            "id" => [
                "required", 
                new ValidateGeolocalizationTemplate($this->request->get('article_id')),
                new ValidateGeolocalizationImage($this->request->get('id')),
            ],
        ];
    }
}
