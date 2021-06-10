<?php

namespace App\Models\Jobs\Scopes;

/**
 * Job Profession Scope
 */
trait ProfessionScope
{
   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeActive($query)
   {
      return $query
         ->whereHas('jobCategoryProfession.JobCategory.brandCategory', function ($subQuery) {
            $subQuery
               ->where('brand_category.brand', request()->brand_id);
         });
   }
}
