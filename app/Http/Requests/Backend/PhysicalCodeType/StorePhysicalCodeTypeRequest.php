<?php

namespace App\Http\Requests\Backend\PhysicalCodeType;

use Illuminate\Foundation\Http\FormRequest;

class StorePhysicalCodeTypeRequest extends FormRequest
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
        $id = $this->request->get('id') != null ? ",".$this->request->get('id') : "";

        return [
            "physical_code_type_name"              => "required|unique:physical_code_type,name->en".$id."|unique:physical_code_type,name->it".$id."|unique:physical_code_type,name->ph-fil".$id."|unique:physical_code_type,name->de".$id."|unique:physical_code_type,name->ph-bis".$id,
        ];
        
    }

    public function messages()
    {
        return [
            'physical_code_type_name.required' => 'Name is required.',
            'physical_code_type_name.unique' => $this->request->get('physical_code_type_name').' is an existing physical code type record',
        ];
    }
}
