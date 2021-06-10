<?php

namespace App\Http\Requests\Backend\InformationType;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformationTypeRequest extends FormRequest
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
            "information_type_name"              => "required|unique:information_type,name->en".$id."|unique:information_type,name->it".$id."|unique:information_type,name->ph-fil".$id."|unique:information_type,name->de".$id."|unique:information_type,name->ph-bis".$id,
            "information_type_data"              => "required"
        ];
        
    }

    public function messages()
    {
        return [
            'information_type_name.required' => 'Name is required.',
            'information_type_data.required' => 'Type of Data is required.',
            'information_type_name.unique' => $this->request->get('information_type_name').' is an existing information type record',
        ];
    }
}
