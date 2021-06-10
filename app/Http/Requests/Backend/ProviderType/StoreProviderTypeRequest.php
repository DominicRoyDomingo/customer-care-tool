<?php

namespace App\Http\Requests\Backend\ProviderType;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderTypeRequest extends FormRequest
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
            "provider_type_name"              => "required|unique:provider_type,name->en".$id."|unique:provider_type,name->it".$id."|unique:provider_type,name->ph-fil".$id."|unique:provider_type,name->de".$id."|unique:provider_type,name->ph-bis".$id,
        ];
        
    }

    public function messages()
    {
        return [
            'provider_type_name.required' => 'Name is required.',
            'provider_type_name.unique' => $this->request->get('provider_type_name').' is an existing provider type record',
        ];
    }
}
