<?php

namespace App\Http\Requests\Backend\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest.
 */
class InviteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return $this->user()->isAdmin();
        return $this->user()->hasRole(['administrator']) || $this->user()->organizations()->first() != null;
    }

    // /**
    //  * Get the validation rules that apply to the request.
    //  *
    //  * @return array
    //  */
    // public function rules()
    // {
    //     return [
    //         'email' => ['required', 'email'],
    //         'first_name' => ['required'],
    //         'last_name' => ['required'],
    //         'roles' => ['required', 'array'],
    //     ];
    // }
}
