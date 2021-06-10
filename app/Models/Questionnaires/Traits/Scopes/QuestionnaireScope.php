<?php

namespace App\Models\Questionnaires\Traits\Scopes;

use DB;

/**
 * Terms Scope
 */
trait QuestionnaireScope
{

   public function scopeMedicalTerm($query, $id)
   {
      return $query->findOrFail($id);
   }

   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeActive($query)
   {
      return $query
         ->join('brand_quesionnaires', function ($join) {
            $join
               ->on('brand_quesionnaires.questionnaire_id', '=', 'questionnaires.id')
               ->where('brand_quesionnaires.brand_id', request()->brand_id);
         });
   }

   public function scopeFiltered($query)
   {
      $request = request();
      $json_key = "$.{$request->locale}";
      $string = "%{$request->filter}%";

      $query
         ->where(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.en'))"), 'like', strtolower($string))
         ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.de'))"), 'like', strtolower($string))
         ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.it'))"), 'like', strtolower($string));
      // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-bis'))"), 'like', strtolower($string))
      // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-fil'))"), 'like', strtolower($string));
   }

   public function scopeSortedByNoLang($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {
         return $query->orderBy('created_at',  $sort);
      }

      $value = '{"' . $value . '":';
      return  $query->where('name', 'not like', "%{$value}%");
   }
}
