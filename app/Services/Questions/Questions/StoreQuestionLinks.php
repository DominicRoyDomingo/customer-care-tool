<?php
namespace App\Services\Questions\Questions;

use App\Helpers\General\ImageHelper;
use App\Models\Questions\QuestionLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class StoreQuestionLinks
{
	/**
     * store question links
     * @param  array $params [description]
     * @return object
     */
    public function execute($params = [], $questionId = null)
    {
        // delete the question link first
        if (!is_null($questionId)) {
            QuestionLink::where("question", $questionId)->delete();  
        }

        if (count($params) > 0) {
            foreach ($params as $param) {
                QuestionLink::create($param);   
            }
        }
    }
}
