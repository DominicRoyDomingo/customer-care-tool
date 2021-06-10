<?php
namespace App\Services\Questions\QuestionTypes;

use App\Models\Questions\QuestionType;
use Illuminate\Database\Eloquent\Builder;

class GetAllQuestionTypesListing
{
    public function execute($request)
    {

        $currentLocale     = $request->locale;
        $orderBy           = $request->orderBy ?? "created_at";
        $sort              = $request->sort ?? "desc";

        return  QuestionType::when($request->sortbyLang, function ($query) use ($request) {
                      $query->sortedByNoLang($request->sortbyLang);
                    })
                    ->when($request->filter, function ($query) {
                      $query->filtered();
                    })
                    ->orderBy($orderBy,  $sort)
                    ->paginate($request->perPage);
    }
}
