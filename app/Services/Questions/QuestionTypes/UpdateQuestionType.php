<?php
namespace App\Services\Questions\QuestionTypes;

use App\Models\Questions\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class UpdateQuestionType
{
	/**
     * Update question type here
     * @param  array $req [description]
     * @return array
     */
    public function execute($req)
    {
        $idUpdate       = $req["id"];
        $lang           = $req["lang"];
        $name           = $req["name"];
        $type_of_data   = $req["type_of_data"];

        $qt = QuestionType::findOrFail($idUpdate);

        $nameVal = string_add_json( $lang, ucwords( $name ), string_remove( $lang, $qt->name ) );

        $qt->name           = $nameVal;
        $qt->type_of_data   = $type_of_data;

        if ($qt->update()) {
            return ["success" => true, "data" => $qt];
        }

        return ["success" => false, "message" => "Error during update."];
    }
}
