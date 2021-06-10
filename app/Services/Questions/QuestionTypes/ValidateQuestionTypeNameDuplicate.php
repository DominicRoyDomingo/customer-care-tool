<?php
namespace App\Services\Questions\QuestionTypes;

use App\Models\Questions\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ValidateQuestionTypeNameDuplicate
{
	/**
     * validate of name exist on update
     * @param  [type] $params [description]
     * @return array
     */
    public function execute($req, $isUpdate = true)
    {
        $lang = $isUpdate ? $req["lang"] : $req["locale"];

        $items = QuestionType::whereNotNull('name')->get();

        $data = get_data($lang, ['name'], $items, $isUpdate);

        $is_data_exist = is_data_exist('name', $req["name"], $data, $isUpdate);
        
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
