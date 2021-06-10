<?php
namespace App\Services\Questions\Questions;


use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ValidateQuestionsQuestionDuplicate
{
	/**
     * validate of name exist on update
     * @param  [type] $params [description]
     * @return array
     */
    public function execute($req, $isUpdate = true)
    {
        $items = Question::whereNotNull('question')->get();
        $data = get_data($req["lang"], ['question'], $items, $isUpdate);

        $is_data_exist = is_data_exist('question', $req["question"], $data, $isUpdate);

        if ($isUpdate) {
            $delete_item = $req["id"];
            if (($key = array_search($delete_item, $is_data_exist)) !== false) {
                unset($is_data_exist[$key]);
            }
            return count($is_data_exist) > 0 ? ["success" => false, "message" => "Duplicate"] : ["success" => true, "message" => "No duplicate"];
        }
       
        return $is_data_exist;


    }
}
