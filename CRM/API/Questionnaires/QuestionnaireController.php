<?php

namespace CRM\API\Questionnaires;

use App\Http\Controllers\Controller;
use App\Models\Questionnaires\Questionnaire;
use CRM\API\Questionnaires\Requests\StoreQuestionnaireRequest;
use DB;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
   private $model;

   public function __construct(Questionnaire $model)
   {
      $this->model = $model;
   }

   public function index(Request $request)
   {
      $data = $this->model->select('questionnaires.*')
         ->active()
         ->when($request->filter, function ($query) {
            $query->filtered();
         })
         ->when($request->sortbyLang, function ($query) use ($request) {
            $request['locale'] = $request->sortbyLang;
            $query->sortedByNoLang($request->sortbyLang);
         })
         ->with(['created_by'])
         ->paginate($request->perPage);

      return response()->json($data);
   }

   public function store(StoreQuestionnaireRequest $request)
   {
      $question = (object) '';
      $user = $request->user();

      $data = [
         'type' => $request->type,
         'creator' => $user->isActive() ? $user->id : null
      ];

      if ($request->action === 'add') {
         $data['name'] = to_json_add($request->locale, $request->name);
         $question = $this->model->create($data);
      } else {
         $model = $this->model->findOrFail($request->id);
         $jsonRmv = to_json_remove($request->locale, $model->name);
         $data['name'] = to_json_add($request->locale, $request->name, $jsonRmv);
         $question = $this->to_update($model, $data);
      }

      return response()->json($question);
   }

   public function destroy(Request $request)
   {
      $model = $this->model->findOrFail($request->id);

      $model->delete();

      return response()->json(true);
   }

   public function get_sequences(Request $request)
   {
      $model = $this->model->find($request->id)
         ->question_sequences()
         ->paginate($request->perPage);

      return response()->json($model);
   }

   public function sort_sequences(Request $request)
   {
      DB::table('questionnaire_content')
         ->where('questionnaire_id', $request->questionnaire_id)
         ->delete();

      $arry = json_decode($request->sequences) ?: [];
      foreach ($arry as $value) {
         DB::table('questionnaire_content')
            ->insert([
               'questionnaire_id' => $request->questionnaire_id,
               'question_id' => $value->question_id,
               'sequence' => $value->sequence
            ]);
      }

      return response()->json(true);
   }

   public function store_question_sequence(Request $request)
   {
      $model = $this->model->find($request->questionnaire_id);
      $lastSequence = $model->question_sequences()->get()->last();

      if (!$lastSequence) {
         DB::table('questionnaire_content')
            ->insert([
               'question_id' => $request->question_id,
               'questionnaire_id' => $request->questionnaire_id,
               'sequence' => 1
            ]);
      } else {
         $model
            ->question_sequences()
            ->attach(
               $request->question_id,
               ['sequence' => ($lastSequence->pivot->sequence + 1)]
            );
      }

      return response()->json(true);
   }

   public function delete_sequences(Request $request)
   {
      DB::table('questionnaire_content')
         ->where('id', $request->questionnaire_content_id)
         ->delete();

      $sequences = $this->model->find($request->id)->question_sequences;
      $count = 1;
      foreach ($sequences as $value) {
         DB::table('questionnaire_content')
            ->where('id', $value->pivot->id)
            ->update([
               'sequence' => $count
            ]);
         $count++;
      }

      return response()->json(true);
   }

   public function to_update($model, array $data): Questionnaire
   {
      $model->name = $data['name'];
      $model->save();

      return $model;
   }
}
