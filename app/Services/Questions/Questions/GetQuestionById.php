<?php
namespace App\Services\Questions\Questions;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;

class GetQuestionById
{
	/**
	 * Get question Type By id
	 * @param  int $id Question Type Id
	 * @return Obj     [description]
	 */
    public function execute($id)
    {
        return Question::find($id);
    }
}
