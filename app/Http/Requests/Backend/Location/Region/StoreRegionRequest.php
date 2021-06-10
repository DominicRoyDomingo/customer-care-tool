<?php

namespace App\Http\Requests\Backend\Location\Region;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateDuplicateRegion;

class StoreRegionRequest extends FormRequest
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
            "region"    => [
                "required", 
                new ValidateDuplicateRegion($this->request->get('country_id'), $id)
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
