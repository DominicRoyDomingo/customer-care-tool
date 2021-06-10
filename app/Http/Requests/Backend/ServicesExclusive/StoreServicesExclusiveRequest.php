<?php

namespace App\Http\Requests\Backend\ServicesExclusive;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicesExclusiveRequest extends FormRequest
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
            "text_display"         => "required|unique:services_exclusive,text_display->".$lang.$id,
            "service"              => "required",
        ];
    }
}
