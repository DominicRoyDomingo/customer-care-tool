<?php

namespace App\Http\Requests\Backend\Location\Division;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateDuplicateDivision;

class StoreDivisionRequest extends FormRequest
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
        $id = $this->request->get('id') != null ? $this->request->get('id') : null;
        return [
            "name"    => [
                "required", 
                new ValidateDuplicateDivision($this->request->get('country_id'), $id)
            ],
        ];
        
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
        ];
    }
}
