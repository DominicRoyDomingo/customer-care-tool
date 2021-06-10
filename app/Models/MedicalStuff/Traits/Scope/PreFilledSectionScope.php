<?php

namespace App\Models\MedicalStuff\Traits\Scope;

use DB;

trait PreFilledSectionScope
{
     public function scopeSortedByNoLang($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {
         return $query->orderBy('created_at',  $sort);
      }
      $value = '"' . $value . '":';
      return  $query->where('name', 'not like', "%{$value}%");
   }

}
