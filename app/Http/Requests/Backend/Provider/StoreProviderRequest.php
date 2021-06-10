<?php

namespace App\Http\Requests\Backend\Provider;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
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
            "name"              => "required|unique:providers,name->".$lang.$id,
            "plan"              => "required",
            "provider_type"     => "required",
            "country"           => "nullable",
            "division"          => "nullable",
            "city"              => "nullable",
            "website"           => "nullable",
            "contact_no"        => "nullable",
            "address"           => "nullable",
            "images"            => "nullable",
            "postal_code"       => "nullable",
            "facebook_url"      => "nullable",
            "linkedin"          => "nullable",
            "info_status"       => "nullable",
            "account_status"    => "nullable",
            "other_info"        => "nullable",
            "brand_id"          => "required",
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => 'Name is required.',
            'plan.required'         => 'Plan is required.',
            'name.unique'         => $this->request->get('name').' already exists.',
            'provider_type.required'   => 'Provider type is required.',
            'provider_type.array'   => 'Provider type is required.',
        ];
    }
}
