<?php

namespace App\Models\Jobs\Scopes;


/**
 * Job Category Scope
 */
trait JobCategoryScope
{
   public function scopeActive($query)
   {
      return $query
         ->join('brand_category', function ($join) {
            $join
               ->on('brand_category.category', '=', 'job_category.id')
               ->where('brand_category.brand', request()->brand_id);
         });
   }
}
