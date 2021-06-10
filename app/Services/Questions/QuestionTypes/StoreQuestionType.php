<?php
namespace App\Services\Questions\QuestionTypes;

use App\Models\Questions\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class StoreQuestionType
{
	/**
	 * Save question type here
	 * @param  array $params [description]
	 * @return Obj
	 */
    public function execute($params)
    {
    	$lang = $params["locale"];

    	$params["name"] = json_encode([$lang => $params["name"]]);

        $save = QuestionType::create($params);
        
        if ($save) {
            return response()->json([
                "success" => true, 
                "data" => $save
            ]);
        }

        return response()->json([
            "success"   => false, 
            "data"      => [], 
            "error"     => "Error during saving type of question."
        ]);
    }
}
