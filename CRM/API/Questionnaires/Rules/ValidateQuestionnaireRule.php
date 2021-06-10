<?php

namespace CRM\API\Questionnaires\Rules;

use App\Models\Questionnaires\Questionnaire;
use Illuminate\Contracts\Validation\Rule;

class ValidateQuestionnaireRule implements Rule
{
    public $locale;
    public $attribute;
    public $value;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($lang)
    {
        $this->locale = $lang;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        $this->attribute = $attribute;
        $items = Questionnaire::whereNotNull('name')->get();
        $data = get_data($this->locale, [$attribute], $items);
        return !is_data_exist($attribute, $value, $data);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $data = __('menus.backend.sidebar.questions');
        return  __('validation.exist', ['attribute' => $this->value, 'data' => $data]);
    }
}
