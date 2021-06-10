<?php

namespace App\Http\Requests\Backend\ProviderService;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderServiceRequest extends FormRequest
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
        return [
            "services"      => "required",
            "providers"     => "required",
            "day_start"     => "required",
            "day_end"       => "nullable",
            "description"   => "nullable",
            "parameter"     => "nullable",
        ];
    }
}
