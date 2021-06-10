<?php

namespace CRM\API\Questionnaires\Requests;

use CRM\API\Questionnaires\Rules\ValidateQuestionnaireRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionnaireRequest extends FormRequest
{
   /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
   public function authorize()
   {
      // return $this->user()->isAdmin();
      return true;
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
   public function rules()
   {
      $isDuplicate = false;
      if ($this->request->get('action') == 'add') {
         $isDuplicate = new ValidateQuestionnaireRule($this->request->get('locale'));
      }

      return [
         'name' => ['required', $isDuplicate],
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
         'name.required' => 'The name is required',
      ];
   }
}
