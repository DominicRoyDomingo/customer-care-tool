<?php

namespace App\Http\Requests\Backend\AlgoliaClass;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlgoliaClassRequest extends FormRequest
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
        $id = $this->request->get('id') != null ? $this->request->get('id') : "";

        return [
            "name"  => "required|unique:classes,name,".$id,
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => 'name is required.',
            'name.unique'  => $this->request->get('name').' is an existing Class record',
        ];
    }
}
