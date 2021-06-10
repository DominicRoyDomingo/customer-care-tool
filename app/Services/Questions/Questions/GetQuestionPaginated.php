<?php

namespace App\Services\Questions\Questions;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;

class GetQuestionPaginated
{
   private $model;

   public function __construct(Question $model)
   {
      $this->model = $model;
   }

   public function execute()
   {
      return $this->model->select('questions.*')
         ->get();
      // ->paginate(request()->perPage);
   }
}
