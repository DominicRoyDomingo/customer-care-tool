<?php


namespace CRM\API\MedicalStuff\Requests;

use CRM\API\MedicalStuff\Rules\ValidateIcon;
use Illuminate\Foundation\Http\FormRequest;

class StoreTermRequest extends FormRequest
{
   /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
   public function authorize()
   {
      return true;
      // return $this->user()->isAdmin();
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
   public function rules()
   {
      $isFile = request()->hasFile('file');
      $isIcon = request()->hasFile('icon');
      return [
         "name" => [
            "required",
         ],
         'file' => $isFile ?  ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'] : [],
         'icon' => $isIcon ?  ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'] : [],
      ];
   }

   /**
    * Get the validation messages that apply to the request.
    *
    * @return array
    */
   public function messages()
   {
      return [
         'name.required' => 'The :attribute  is required',
      ];
   }
}
