<?php

namespace App\Http\Requests\Backend\HtmlTagPriority;

use Illuminate\Foundation\Http\FormRequest;

class StoreHtmlTagPriorityRequest extends FormRequest
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
        $id = $this->request->get('id') != null ? $this->request->get('id') : "";

        return [
            "description"   => "required|unique:html_tags,description,".$id,
        ];
    }

                    
    public function messages()
    {
        return [
            'description.required'  => 'Description is required.',
            'description.unique'  => $this->request->get('description').' is an existing HTML Tag Priority record',
        ];
    }
}
