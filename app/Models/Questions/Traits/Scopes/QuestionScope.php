<?php

namespace App\Models\Questions\Traits\Scopes;

use DB;

/**
 * Question Scope
 */
trait QuestionScope
{
   public function scopeSortedByNoLang($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {
        return $query->orderBy('created_at',  $sort);
      } else {
        request()['locale'] = $value;
        $value = '{"' . $value . '":';
        return  $query->where('question', 'not like', "%{$value}%");
      }
   }

   public function scopeFiltered($query)
   {
      $request = request();
      $json_key = "$.{$request->locale}";
      $string = "%{$request->filter}%";
      $query
          ->whereRaw("LOWER(json_unquote(json_extract(`questions`.`question`, '$.\"en\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`questions`.`question`, '$.\"de\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`questions`.`question`, '$.\"it\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`questions`.`question`, '$.\"ph-bis\"'))) LIKE LOWER('{$string}')")
          ->orWhereRaw("LOWER(json_unquote(json_extract(`questions`.`question`, '$.\"ph-fil\"'))) LIKE LOWER('{$string}')");
   }
}
