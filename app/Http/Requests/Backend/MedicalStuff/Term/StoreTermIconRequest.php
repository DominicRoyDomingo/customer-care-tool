<?php

namespace App\Http\Requests\Backend\MedicalStuff\Term;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateTermProviderType;

class StoreTermIconRequest extends FormRequest
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
            "term"    => [
                "required", 
            ],
            "provider_type" => [
                "required",
                new ValidateTermProviderType($this->request->get('term'), $this->request->get('imagePlaceholders'))
            ],
            "icon"    => [
                "required", 
            ],
        ];
    }

    public function messages()
    {
        return [
            'provider_type.required' => 'Term is required.',
            'icon.required' => 'Icon is required.',
        ];
    }
}
