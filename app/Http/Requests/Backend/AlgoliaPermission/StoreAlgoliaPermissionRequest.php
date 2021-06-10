<?php

namespace App\Http\Requests\Backend\AlgoliaPermission;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateBrandAndClassDuplicate;

class StoreAlgoliaPermissionRequest extends FormRequest
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
        $id = $this->request->get('id') != null ? ",".$this->request->get('id') : "";
        
        return [
            "brand"                 => [
                "required", 
                new ValidateBrandAndClassDuplicate($this->request->get('class'), $this->request->get('id'))
            ],
            "class"                 => "required",
            "staging_index_name"    => "required",
            "live_index_name"       => "required",
            "sync"                  => "required",
        ];
    }

    public function messages()
    {
        return [
            'brand.required'                => 'brand_required',
            'class.required'                => 'class_required',
            'staging_index_name.required'   => 'staging_index_name_required',
            'live_index_name.required'      => 'live_index_name_required',
        ];
    }
}
