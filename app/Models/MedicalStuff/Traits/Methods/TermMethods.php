<?php

namespace App\Models\MedicalStuff\Traits\Methods;

use App\Models\MedicalStuff\TermConnectionDescription;
use DB;

/**
 * Article Methods
 */
trait TermMethods
{
   public function term_descriptions(int $parentId, int $childId)
   {
      return TermConnectionDescription::select('term_connection_descriptions.*')
         ->where('term_id',  $parentId)
         ->where('linked_term_id',  $childId)
         ->with(['term_description', 'term'])
         ->get()
         ->map(function ($item) {
            return $item;
         })
         ->filter(function ($item) {
            return $item;
         })
         ->values();
   }
}
