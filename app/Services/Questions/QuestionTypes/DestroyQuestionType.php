<?php
namespace App\Services\Questions\QuestionTypes;

use App\Models\Questions\QuestionType;

class DestroyQuestionType
{
	/**
	 * Save question type here
	 * @param  array $params [description]
	 * @return Obj
	 */
    public function execute($id)
    {
        return QuestionType::where('id', $id)->delete();
    }
}
