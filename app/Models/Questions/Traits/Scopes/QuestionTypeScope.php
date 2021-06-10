<?php

namespace App\Models\Questions\Traits\Scopes;

use DB;

/**
 * Question Scope
 */
trait QuestionTypeScope
{
   public function scopeSortedByNoLang($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {
        return $query->orderBy('created_at',  $sort);
      } else {
        request()['locale'] = $value;
        $value = '{"' . $value . '":';
        return  $query->where('name', 'not like', "%{$value}%");
      }
   }

   public function scopeFiltered($query)
   {
      $request = request();
      $json_key = "$.{$request->locale}";
      $string = "%{$request->filter}%";
      $query
          ->whereRaw("LOWER(json_unquote(json_extract(`question_types`.`name`, '$.\"en\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`question_types`.`name`, '$.\"de\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`question_types`.`name`, '$.\"it\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`question_types`.`name`, '$.\"ph-bis\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`question_types`.`name`, '$.\"ph-fil\"'))) LIKE LOWER('{$string}')");
   }
}
