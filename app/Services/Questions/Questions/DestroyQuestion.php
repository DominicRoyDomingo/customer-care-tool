<?php
namespace App\Services\Questions\Questions;

use App\Models\Questions\Question;

class DestroyQuestion
{
	/**
	 * Delete question here
	 * @param  array $params [description]
	 * @return boolean
	 */
    public function execute($id)
    {
        return Question::where('id', $id)->delete();
    }
}
