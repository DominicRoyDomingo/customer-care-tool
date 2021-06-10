<?php
namespace App\Services\Questions\Questions;

use App\Helpers\General\ImageHelper;
use App\Models\Questions\Question;
use App\Services\Questions\Questions\GetQuestionById;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class StoreQuestionBrand
{
    private $getQuestionById;

    public function __construct(GetQuestionById $getQuestionById)
    {
        $this->getQuestionById = $getQuestionById;
    }

    /**
     * Save question brand here
     * @param  int $id [description]
     * @param  array $params [description]
     * @return json
     */
    public function execute($id,$request)
    {
      try {
        // get the question by id
        $question = $this->getQuestionById->execute($id);

        // payload here
        $reqData = $request->all();

        $brand_ids = [];

        if (!is_null($reqData["brand_ids"])) {
          // explode
          $brand_ids = explode(",", $reqData["brand_ids"]);

          // remove all assigned brands from question
          $question->brand()->detach();

          // insert all the question brands
          $question->brand()->attach($brand_ids);
        }else{

          // remove all assigned brands from question
          $question->brand()->detach();
        }
        return response()->json(["success" => true]);
      } catch (Exception $e) {
        return response()->json(["success" => false]);
      }
    }
}