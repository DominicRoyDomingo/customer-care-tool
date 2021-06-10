<?php

namespace App\Models\MedicalStuff\Traits\Methods;

use DB;

/**
 * Article Methods
 */
trait ArticleMethods
{
   public function article_terms()
   {
      $items = [];
      foreach ($this->medical_terms as $term) {
         if ($termTypes = $term->has_term_types) {
            $count = 0;
            foreach ($termTypes as $termType) {
               $items[$termType->term_type_name][] = [
                  'id' => $term->id,
                  'name' => $term->term_name,
                  'id_slug_name' => str_slug("{$term->base_name} {$count}"),
                  'route_show' => $term->route_show,
               ];
               $count++;
            }
         }
      }
      return $items;
   }

   public function article_course_terms()
   {
      # code...
   }
}
