<?php

namespace App\Http\Requests\Backend\ProviderGroup;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderGroupRequest extends FormRequest
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
        $lang   = $this->request->get('lang');
        $id     = $this->request->get('id') != null ? ",".$this->request->get('id') : "";
        return [
            "provider_group_name"   => "required|unique:provider_groups,name->en".$id."|unique:provider_groups,name->it".$id."|unique:provider_groups,name->ph-fil".$id."|unique:provider_groups,name->de".$id."|unique:provider_groups,name->ph-bis".$id,
            "image"                 => "nullable",
        ];
    }

    public function messages()
    {
        return [
            'provider_group_name.required' => 'Name is required.',
            'provider_group_name.unique' => $this->request->get('provider_group_name').' is an existing provider group record',
        ];
    }
}
