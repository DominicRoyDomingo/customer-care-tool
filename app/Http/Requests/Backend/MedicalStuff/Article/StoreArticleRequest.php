<?php

namespace App\Http\Requests\Backend\MedicalStuff\Article;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateHTMLTag;

class StoreArticleRequest extends FormRequest
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
            "title"    => [
                "required", 
            ],
            "images" => [
                "nullable",
                new ValidateHTMLTag($this->request->get('tags'), $this->request->get('imagePlaceholders'))
            ]
        ];
    }
}
