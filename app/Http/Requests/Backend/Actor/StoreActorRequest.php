<?php

namespace App\Http\Requests\Backend\Actor;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateActor;
use App\Rules\ValidateIdentificationCode;

class StoreActorRequest extends FormRequest
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
            "surname"               => [
                "required", 
                new ValidateActor(
                    $this->request->get('surname'), 
                    $this->request->get('firstname'), 
                    $this->request->get('middlename'),
                    $this->request->get('id')
                )
            ],
            "firstname"             => "nullable",
            "middlename"            => "nullable",
            "physical_code"         => [
                "nullable",
                new ValidateIdentificationCode($this->request->get('brand_id'),$this->request->get('id'))
            ],
            "other_info"            => "nullable",
            "field_of_professions"  => "nullable",
            "brand_id"              => "required",
        ];
    }

                    
    public function messages()
    {
        return [
            'surname.required'  => 'Surname is required.',
        ];
    }
}
