<?php

namespace App\Http\Requests\Backend\GeolocalizationTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeolocalizationTemplateRequest extends FormRequest
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
        return [
            "article_id"            => "required",
            "title"                 => "nullable",
            "body"                  => "nullable",
            "meta_description"      => "nullable",
            "slug"                  => "nullable",
            "alt_tag_image"         => "nullable",
            "image_description"     => "nullable",
        ];
    }
}
