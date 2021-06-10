<?php


namespace CRM\API\MedicalStuff\Traits;

use App\Helpers\General\CollectionHelper;
use App\Models\MedicalStuff\MedicalTerm;

/**
 * Terms Items
 */
trait ItemsTrait
{
   function get_default_items($paginated)
   {
      return $paginated
         ->getCollection()
         ->map(function ($value) {
            return $value;
         })
         ->filter(function ($item) {
            return $item;
         })
         ->values();
   }

   public function get_filter_items($paginated)
   {
      $request = request();

      return $paginated
         ->getCollection()
         ->map(function ($item) use ($request) {
            if (strlen($request->filter) > 0 && false !== stristr($item->term_name, $request->filter)) {
               return $item;
            }
         })
         ->filter(function ($item) {
            return $item;
         })
         ->values();
   }

   public function get_search_items($paginated)
   {
      $request = request();

      return $paginated
         ->getCollection()
         ->map(function ($term) use ($request) {
            if ($request->type_terms && $request->specializations) {
               if ($this->check_term_types($term,  $request->type_terms) && $this->check_specializations($term,  $request->specializations)) {
                  return $term;
               }
            } else if (is_array($request->type_terms) && $this->check_term_types($term, $request->type_terms)) {
               return $term;
            } else if (is_array($request->specializations) && $this->check_specializations($term, $request->specializations)) {
               return $term;
            }

            if (is_array($request->terms) && $this->check_terms($term, $request->terms)) {
               return $term;
            }

            if (is_array($request->articles) && $this->check_articles($term, $request->articles)) {
               return $term;
            }
         })
         ->filter(function ($value) {
            return $value;
         })
         ->values();
   }

   // public function get_sorted_lang_items($paginated, $value)
   // {
   //    return $paginated
   //       ->getCollection()
   //       ->map(function ($item) use ($value) {
   //          switch ($value) {
   //             case 'no-tt':
   //                if (!$item->has_term_types) return $item;
   //                break;
   //             case 'no-spec':
   //                if (!$item->has_specializations) return $item;
   //                break;
   //             case 'no-la':
   //                if ($item->medical_articles->count() === 0) return $item;
   //                break;
   //             case 'no-lt':
   //                if (!$item->has_terms_id) return $item;
   //                break;
   //             case 'all':
   //                return $item;
   //             default:
   //                if (!$item->has_translation) return $item;
   //                break;
   //          }
   //       })
   //       ->filter(function ($item) {
   //          return $item;
   //       })
   //       ->values();
   // }

   // sorted items 
   // public function sorted_terms($request)
   // {
   //    return $this->model
   //       ->select('medical_terms.*')
   //       ->active()
   //       ->with([
   //          'medical_articles',
   //          'brands',
   //          'service'
   //       ])
   //       ->when($request->sortbyTerm, function ($query) use ($request) {
   //          $query->sortedByTerm($request->sortbyTerm);
   //       })

   //       ->paginate($request->perPage);
   // }

   // public function sorted_no_language($request)
   // {
   //    return $this->model
   //       ->select('medical_terms.*')
   //       ->active()
   //       ->with([
   //          'medical_articles',
   //          'brands',
   //          'service'
   //       ])
   //       ->sortedByNoLang($request->sortbyLang)

   //       ->paginate($request->perPage);
   // }



   // public function get_sorted_term_items($paginated, $value)
   // {
   //    if ($value === 'all') {
   //       return  $this->get_default_items($paginated);
   //    }

   //    $arr = explode('_', $value);
   //    $sortBy = $arr[0] === 'most' ? 'sortByDesc' : 'sortBy';

   //    $terms = $paginated
   //       ->getCollection()
   //       ->$sortBy(function ($item) use ($value) {
   //          switch ($value) {
   //             case 'least_lt':
   //                if ($item->has_terms_id) return count($item->has_terms_id);
   //                return 0;
   //             case 'most_lt':
   //                if ($item->has_terms_id) return count($item->has_terms_id);
   //                return 0;
   //             case 'least_la':
   //                return count($item->medical_articles);
   //             case 'most_la':
   //                return count($item->medical_articles);
   //             case 'least_tt':
   //                if ($item->has_term_types) return count($item->has_term_types);
   //                return 0;
   //             case 'most_tt':
   //                if ($item->has_term_types) return count($item->has_term_types);
   //                return 0;
   //                if ($item->has_specializations) return count($item->has_specializations);
   //                return 0;
   //             case 'most_spec':
   //                if ($item->has_specializations) return count($item->has_specializations);
   //                return 0;
   //          }
   //       });


   //    return $terms->values();
   // }
}
