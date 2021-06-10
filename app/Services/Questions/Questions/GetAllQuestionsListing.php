<?php
namespace App\Services\Questions\Questions;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;

class GetAllQuestionsListing
{
    public function execute($request)
    {
      $currentLocale     = $request->locale;
      $orderBy           = $request->orderBy ?? "created_at";
      $sort              = $request->sort ?? "desc";

      return  Question::with('brand')->with(['question_links' => function($q){
                    $q->with('choice');
                }])
                ->with('question_type')
                ->when($request->sortbyLang, function ($query) use ($request) {
                  $query->sortedByNoLang($request->sortbyLang);
                })
                ->when($request->filter, function ($query) {
                  $query->filtered();
                })
                ->orderBy($orderBy,  $sort)
                ->paginate($request->perPage);
    }
}
