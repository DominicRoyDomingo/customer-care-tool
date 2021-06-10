<?php
namespace App\Services\Questions\QuestionTypes;

use App\Models\Questions\QuestionType;
use Illuminate\Database\Eloquent\Builder;

class GetQuestionTypeById
{
	/**
	 * Get question Type By id
	 * @param  int $id Question Type Id
	 * @return Obj     [description]
	 */
    public function execute($id)
    {
        return QuestionType::find($id);
    }
}
