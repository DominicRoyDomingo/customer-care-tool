<?php

namespace App\Models\MedicalStuff\Traits\Scope;

use App\Models\MedicalStuff\MedicalTermType;
use DB;

/**
 * Terms Scope
 */
trait TermScope
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
         ->join('brand_terms', function ($join) {
            $join
               ->on('medical_terms.id', '=', 'brand_terms.terminology_id')
               ->where('brand_terms.brand_id', request()->brand_id);
         });
   }

   /**
    * @param $query
    * @param string $category
    *
    * @return mixed
    */
   public function scopeTermCategory($query, $category)
   {
      return $query->whereNotNull($category);
   }

   /**
    * @param $query
    * @param string $Sorted 
    *
    * @return mixed
    */
   public function scopeSortedByLinkedTerm($query, $sort = 'desc')
   {
      if (request()->medical_terms) {

         $string = trim(request()->medical_terms, '[]');

         // Filter by Medical Terms String
         return $query->orderByRaw("FIELD(medical_terms.id,{$string}) {$sort}");
      }
   }

   /**
    * @param $query
    * @param string $value
    * @param string $sorted
    *
    * @return mixed
    */
   public function scopeSortedByTerm($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {

         return $query->orderBy('created_at', $sort);
      } else {

         $array = explode('_', $value);
         if ($array[0] !== 'most') {
            $sort = 'asc';
         }

         if ($value === 'least_la' || $value === 'most_la') {
            return  $query
               ->withCount('medical_articles')
               ->orderBy('medical_articles_count', $sort);
         } else {
            $fieldName = 'linked_term_type_id';

            if ($value === 'least_lt' || $value === 'most_lt') {
               $fieldName = 'linked_term_id';
            } elseif ($value === 'most_spec' || $value === 'most_spec') {
               $fieldName = 'linked_specialization_id';
            }

            return  $query
               ->orderBy(
                  DB::raw("(CHAR_LENGTH({$fieldName}) - CHAR_LENGTH(REPLACE({$fieldName}, ',', '')) + 1)"),
                  $sort
               );
         }
      }

      return  $query;
   }

   public function scopeSortedByNoLang($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {
         return $query->orderBy('created_at',  $sort);
      } else {
         $array = explode('-', $value);

         if (count($array) > 1 && $array[0] !== 'ph') {
            if ($array[1] == 'la') {
               return $query
                  ->doesntHave('medical_articles');
            } else {
               $fieldName = 'term_types';

               if ($array[1] == 'spec') {
                  $fieldName = 'specializations';
               } else if ($array[1] == 'lt') {
                  $fieldName = 'medical_terms';
               }
               return $query->whereNull($fieldName);
            }
         } else {
            request()['locale'] = $value;
            $value = '{"' . $value . '":';
            return  $query->where('name', 'not like', "%{$value}%");
         }
      }
   }

   public function scopeSortedByArticleTerm($query, $id, $sort = 'desc')
   {
      $query
         ->leftJoin('medical_term_article_link', 'medical_term_article_link.medical_term_id', '=', 'medical_terms.id')
         // ->groupByRaw('medical_terms.id, medical_term_article_link.medical_term_id')
         ->orderByRaw("FIELD(medical_term_article_link.medical_article_id,{$id}) {$sort}");
   }

   public function scopeCheckBySpecilization($query)
   {
      $specId = $this->to_array(request()->specializations);

      $query->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(specializations, '$.specializations'), '{$specId[0]}')");

      for ($i = 1; $i <= count($specId) - 1; $i++) {
         $query->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(specializations, '$.specializations'), '{$specId[$i]}')");
      }

      return $query;
   }

   public function scopeCheckByTerm($query)
   {
      $termId = $this->to_array(request()->terms);

      $query->orWhereJsonContains('medical_terms', $termId[0]);

      for ($i = 1; $i <= count($termId) - 1; $i++) {
         $query->orWhereJsonContains('medical_terms', $termId[$i]);
      }

      return $query;
   }

   public function scopeCheckByTypeTerm($query)
   {
      $typeTermId = $this->to_array(request()->type_terms);

      $query->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(term_types, '$.term_types'), '{$typeTermId[0]}')");

      for ($i = 1; $i <= count($typeTermId) - 1; $i++) {
         $query->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(term_types, '$.term_types'), '{$typeTermId[$i]}')");
      }

      return $query;
   }

   public function scopeCheckByArticle($query)
   {
      $articleId = $this->to_array(request()->articles);

      return $query->orWhereHas('medical_articles', function ($subQuery) use ($articleId) {
         $subQuery->whereIn('medical_articles.id',  $articleId);
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

   public function to_array($data): array
   {
      return collect($data)
         ->map(
            function ($id) {
               return (int)$id;
            }
         )->toArray();
   }

   public function scopeFilterByTermTypes($query, $type)
   {
      $id = '';
      $termType1 = 'Disciplina di Studi ECM';
      $termType2 = 'Discipline of ECM Studies';

      if ($type === 'course_recepient') {
         $termType1 = 'Professional';
         $termType2 = 'Professionista';
      }

      $mtt = MedicalTermType::where('name', 'like', "%{$termType1}%")
         ->orWhere('name', 'like', "%{$termType2}%")
         ->first();

      if ($mtt) {
         $id = $mtt->id;
      }

      $query->whereRaw("JSON_CONTAINS(JSON_EXTRACT(term_types, '$.term_types'),'{$id}')");
   }
   public function scopeOrderByLinkCourseTermType($query, $id, $sort = 'desc')
   {
      if (request()->type === 'course_discipline') {
         $query
            ->leftJoin('course_discipline', 'course_discipline.term_id', '=', 'medical_terms.id')
            ->orderByRaw("FIELD(course_discipline.article_id,{$id}) {$sort}");
      } else {
         $query
            ->leftJoin('course_recepient', 'course_recepient.term_id', '=', 'medical_terms.id')
            ->orderByRaw("FIELD(course_recepient.article_id,{$id}) {$sort}");
      }
   }
}
