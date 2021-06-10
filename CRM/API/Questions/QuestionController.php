<?php

namespace CRM\API\Questions;

use App\Http\Controllers\Controller;
use App\Services\Questions\Questions\DestroyQuestion;
use App\Services\Questions\Questions\GetAllQuestionsListing;
use App\Services\Questions\Questions\GetQuestionById;
use App\Services\Questions\Questions\GetQuestionPaginated;
use App\Services\Questions\Questions\StoreQuestion;
use App\Services\Questions\Questions\StoreQuestionBrand;
use App\Services\Questions\Questions\UpdateQuestion;
use App\Services\Questions\Questions\ValidateQuestionsQuestionDuplicate;
use App\Services\Questions\Terms\LinkTerms;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

  protected $linkTerms;

  function __construct(LinkTerms $terms)
  {
    $this->linkTerms = $terms;
  }

  /**
   * Get list of questions
   * @param  Request                    $request                    [description]
   * @param  GetAllQuestionsListing $GetAllQuestionsListing [description]
   * @return array                                                  [description]
   */
  public function index(Request $request, GetAllQuestionsListing $getAllQuestionsListing)
  {
    return response()->json($getAllQuestionsListing->execute($request));
  }

  /**
   * Store Question
   * @param  Request           $request           [description]
   * @param  StoreQuestion $StoreQuestion [description]
   * @return obj                                  [description]
   */
  public function store(Request $request, StoreQuestion $storeQuestion, ValidateQuestionsQuestionDuplicate $validateQuestionsQuestionDuplicate)
  {
    $req = $request->all();

    $isUpdate = false;

    $isDuplicate = $validateQuestionsQuestionDuplicate->execute($req, $isUpdate);

    if ($isDuplicate) {
      return response()->json([
        'success' => false,
        'error' => "duplicate"
      ]);
    }

    return response()->json(['success' => true, "data" => $storeQuestion->execute($req)]);
  }

  /**
   * Update question
   * @param  Request $request payloads 
   * @return obj
   */
  public function update($id, Request $request, UpdateQuestion $updateQuestion, ValidateQuestionsQuestionDuplicate $validateQuestionsQuestionDuplicate)
  {
    $req = $request->all();
    $isUpdate = true;

    $response = $validateQuestionsQuestionDuplicate->execute($req, $isUpdate);
    if (!$response["success"]) {
      return response()->json([
        'success' => false,
        'error' => "duplicate"
      ]);
    }
    // updatehere
    return $updateQuestion->execute($req);
  }

  /**
   * Remove/Delete question
   * @param  [type]              $id                  [description]
   * @param  DestroyQuestion $destroyQuestion [description]
   * @return boolean
   */
  public function destroy($id, DestroyQuestion $destroyQuestion)
  {
    return $destroyQuestion->execute($id);
  }

  /**
   * Get Question by id
   * @param  int               $id                  [description]
   * @param  DestroyQuestion $destroyQuestion [description]
   * @return json
   */
  public function show($id, GetQuestionById $getQuestionById)
  {
    return response()->json($getQuestionById->execute($id));
  }

  // for questionnnaire page
  public function get_questions(Request $request, GetQuestionPaginated $questions)
  {
    return response()->json($questions->execute());
  }

  public function get_all_terms(Request $r)
  {

    $result = $this->linkTerms->getAllTerms([
      'current' => $r->current,
      'limit' => $r->limit,
      'lang' => $r->lang,
      'terms' => $r->terms
    ]);

    return response()->json($result);
  }

  public function search_terms(Request $r)
  {

    $result = $this->linkTerms->searchTerms([
      'current' => $r->current,
      'limit' => $r->limit,
      'lang' => $r->lang,
      'terms' => $r->terms,
      'search' => $r->search
    ]);

    return response()->json($result);
  }


  public function updateQuestionTerms(Request $r)
  {

    $result = $this->linkTerms->updateQuestionTerms([
      'id' => $r->id,
      'term' => json_decode($r->term)
    ]);

    return response()->json($result);
  }

  public function getQuestionTerms($question_id)
  {

    $result = $this->linkTerms->singleQuestionTerms([
      'id' => $question_id
    ]);

    return response()->json($result);
  }

  /**
   * Store question brand
   * @param  int             $question_id        [description]
   * @param  Request            $request            [description]
   * @param  StoreQuestionBrand $storeQuestionBrand [description]
   * @return json                                 [description]
   */
  public function storeQuestionBrand($question_id, Request $request, StoreQuestionBrand $storeQuestionBrand){
    return $storeQuestionBrand->execute($question_id, $request);
  }
}
