<?php

namespace App\Http\Requests\Backend\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            "name"                      => "required|unique:services,name->en".$id."|unique:services,name->it".$id."|unique:services,name->ph-fil".$id."|unique:services,name->de".$id."|unique:services,name->ph-bis".$id,
            "service_type"              => "required",
            "images"                    => "nullable",
            "description"               => "nullable",
            "brand_id"                  => "required",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'name.unique' => $this->request->get('name').' is an existing service record',
            'service_type.required' => 'Service type is required.',
        ];
    }
}
