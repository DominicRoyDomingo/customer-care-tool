<?php

namespace App\Http\Requests\Backend\ServiceType;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceTypeRequest extends FormRequest
{
    // public $test;
    // public function __construct() {
    //     $this->test = $this->input('name');
    
    //     dd($this->test);
    // }
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
            "service_type_name"                      => "required|unique:service_type,name->en".$id."|unique:service_type,name->it".$id."|unique:service_type,name->ph-fil".$id."|unique:service_type,name->de".$id."|unique:service_type,name->ph-bis".$id,
            "images"                    => "nullable",
        ];
    }

    public function messages()
    {
        return [
            'service_type_name.required' => 'Name is required.',
            'service_type_name.unique' => $this->request->get('name').' is an existing service type record',
        ];
    }
}
