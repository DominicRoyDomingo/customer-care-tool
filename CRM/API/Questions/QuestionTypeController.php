<?php

namespace CRM\API\Questions;

use App\Http\Controllers\Controller;
use App\Models\Questions\QuestionType;
use App\Services\Questions\QuestionTypes\DestroyQuestionType;
use App\Services\Questions\QuestionTypes\GetAllQuestionTypesListing;
use App\Services\Questions\QuestionTypes\GetQuestionTypeById;
use App\Services\Questions\QuestionTypes\StoreQuestionType;
use App\Services\Questions\QuestionTypes\UpdateQuestionType;
use App\Services\Questions\QuestionTypes\ValidateQuestionTypeNameDuplicate;
use Illuminate\Http\Request;

class QuestionTypeController extends Controller
{
	/**
	 * Get list of question type
	 * @param  Request                    $request                    [description]
	 * @param  GetAllQuestionTypesListing $getAllQuestionTypesListing [description]
	 * @return array                                                  [description]
	 */
   	public function index(Request $request, GetAllQuestionTypesListing $getAllQuestionTypesListing)
   	{
      	return response()->json($getAllQuestionTypesListing->execute($request));  
   	}

   	/**
   	 * Store Question type
   	 * @param  Request           $request           [description]
   	 * @param  StoreQuestionType $storeQuestionType [description]
   	 * @return obj                               	[description]
   	 */
   	public function store(Request $request, StoreQuestionType $storeQuestionType, ValidateQuestionTypeNameDuplicate $validateQuestionTypeNameDuplicate)
   	{
         $req = $request->all();
         $isUpdate = false;

         $isDuplicate = $validateQuestionTypeNameDuplicate->execute($req, $isUpdate);
        
         if ($isDuplicate) {
            return ['duplicate' => true];
         }

      	return $storeQuestionType->execute($req);
   	}

   	/**
   	 * Update question type
   	 * @param  Request $request payloads 
   	 * @return obj
   	 */
   	public function update($id, Request $request, UpdateQuestionType $updateQuestionType, ValidateQuestionTypeNameDuplicate $validateQuestionTypeNameDuplicate)
   	{
         $req = $request->all();

         $response = $validateQuestionTypeNameDuplicate->execute($req);
         if (!$response["success"]) {
            return response()->json($response);
         }
   	    // updatehere
         return $updateQuestionType->execute($req);
   	}

   	/**
   	 * Remove/Delete question type
   	 * @param  [type]              $id                  [description]
   	 * @param  DestroyQuestionType $destroyQuestionType [description]
   	 * @return boolean
   	 */
   	public function destroy($id, DestroyQuestionType $destroyQuestionType)
   	{
      	return $destroyQuestionType->execute($id);
   	}

   	/**
   	 * Get Question type by id
   	 * @param  int               $id                  [description]
   	 * @param  DestroyQuestionType $destroyQuestionType [description]
   	 * @return json
   	 */
   	public function show($id, GetQuestionTypeById $getQuestionTypeById)
   	{
      	return response()->json($getQuestionTypeById->execute($id));
   	}
}
