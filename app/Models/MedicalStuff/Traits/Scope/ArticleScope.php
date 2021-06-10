<?php

namespace App\Models\MedicalStuff\Traits\Scope;

use DB;

/**
 * Article Scope
 */
trait ArticleScope
{
   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeActive($query)
   {
      return $query
         ->join('brand_articles', function ($join) {
            $join
               ->on('brand_articles.article_id', '=', 'medical_articles.id')
               ->where('brand_articles.brand_id', request()->brand_id);
         });
   }

   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeCheckTypeDate($query)
   {
      $request = request();

      return $query->whereHas('type_dates', function ($subQuery) use ($request) {
         $startDate = carbon($request->from)->format(sql_date_format());
         $endDate = carbon($request->to)->format(sql_date_format());
         $subQuery
            ->where('type_dates.id',  $request->type_dates)
            ->orWhereBetween('article_dates.article_date', [$startDate, $endDate]);
      });
   }

   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeCheckTerms($query)
   {
      return $query->whereHas('medical_terms', function ($subQuery) {
         $subQuery->whereIn('medical_terms.id',  request()->terms);
      });
   }

   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeCheckItemTypes($query)
   {
      return $query->whereHas('publishing_item_type_articles', function ($subQuery) {
         $subQuery->whereIn('publishing_item_type.id', request()->item_types);
      });
   }

   /**
    * @param $query
    *
    * @return mixed
    */
   public function scopeCheckAuthors($query)
   {
      return $query->whereHas('author_slot', function ($subQuery) {

         $authorId = collect(request()->authors)
            ->map(function ($id) {
               return (int)$id;
            })
            ->toArray();

         $subQuery->whereJsonContains('authors', $authorId[0]);

         for ($i = 1; $i <= count($authorId) - 1; $i++) {
            $subQuery->orWhereJsonContains('authors', $authorId[$i]);
         }

         return $subQuery;
      });
   }

   public function scopeFiltered($query)
   {
      $request = request();
      $json_key = "$.{$request->locale}";
      $string = "%{$request->filter}%";

      return $query
         ->where(DB::raw("LOWER(JSON_EXTRACT(`title`, '$.en'))"), 'like', strtolower($string))
         ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`title`, '$.de'))"), 'like', strtolower($string))
         ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`title`, '$.it'))"), 'like', strtolower($string));
      // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-bis'))"), 'like', strtolower($string))
      // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-fil'))"), 'like', strtolower($string));
   }

   public function scopeFilteredByGeolocalization($query)
   {
      $request = request();
      $filterByGeolocalization = $request->filterByGeolocalization;
      if ($filterByGeolocalization === 'all') {
         return $query;
      }

      return $query->where(function ($subQuery) use ($filterByGeolocalization) {
         switch ($filterByGeolocalization) {
            case 'no-gc':
               $subQuery->whereDoesntHave('geolocalizations', function ($query) {
                  $query->where('area', 'City');
               });
               break;
            case 'no-gp':
               $subQuery->whereDoesntHave('geolocalizations', function ($query) {
                  $query->where('area', 'Province');
               });
               break;
            case 'no-gr':
               $subQuery->whereDoesntHave('geolocalizations', function ($query) {
                  $query->where('area', 'Region');
               });
               break;
            case 'no-t':
               $subQuery->doesntHave('geolocalization_template');
               break;
            case 'wuga':
               $subQuery->whereHas('geolocalizations', function ($query) {
                  $query->where('publish_status', 'Unpublished');
               });
               break;
            case 'wpga':
               $subQuery->whereHas('geolocalizations', function ($query) {
                  $query->where('publish_status', 'Published');
               });
               break;
            default:
               $subQuery->where('title', 'not like', "%{$filterByGeolocalization}%");
               break;
         }
      });
   }

   public function scopeArticles($query)
   {
      $articleKeywords = ['Articolo Geolocalizzato', 'Articoli Geolocalizzati', 'Geolocalized Articles'];
      return $query->where(function ($subQuery) use ($articleKeywords) {
         $subQuery->whereHas('publishing_item_type_articles', function ($query) use ($articleKeywords) {
            $query->whereIn('type_name->en', $articleKeywords)
               ->orWhereIn('type_name->de', $articleKeywords)
               ->orWhereIn('type_name->it', $articleKeywords)
               ->orWhereIn('type_name->ph-bis', $articleKeywords)
               ->orWhereIn('type_name->ph-fil', $articleKeywords);
         });
      });
   }

   public function scopeSearchByTopic($query)
   {
      $request = request();
      $json_key = "$.{$request->locale}";
      $string = "$request->searchTopic}%";

      return $query
         ->where(DB::raw("LOWER(JSON_EXTRACT(`title`, '$.en'))"), 'like', strtolower($string))
         ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`title`, '$.de'))"), 'like', strtolower($string))
         ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`title`, '$.it'))"), 'like', strtolower($string));
      // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-bis'))"), 'like', strtolower($string))
      // ->orWhere(DB::raw("LOWER(JSON_EXTRACT(`name`, '$.ph-fil'))"), 'like', strtolower($string));
   }


   public function scopeSortedByName($query, $value, $sort)
   {
      $sortBy = $sort == "true" ? 'DESC' : 'ASC';
      switch ($value) {
         case 'topic':
            return $query->orderBy('title',  $sortBy);
            break;
         case 'number_of_geolocalizations':
            return $query->orderBy('geolocalizations_count', $sortBy);
            break;
      }
   }

   public function scopeSortedByNoLang($query, $value, $sort = 'desc')
   {
      if ($value == 'all') {
         return $query->orderBy('created_at',  $sort);
      }

      $value = '{"' . $value . '":';
      return  $query->where('title', 'not like', "%{$value}%");
   }

   public function scopeSortedByTerm($query, $id, $sort = 'desc')
   {
      if (request()->medical_article_id) {
         $string = trim(request()->medical_article_id, '[]');
         return $query
            ->leftJoin('medical_term_article_link', 'medical_term_article_link.medical_article_id', '=', 'medical_articles.id')
            ->orderByRaw("FIELD(medical_articles.id,{$string}) {$sort}")
            ->groupByRaw('medical_articles.id, medical_article_id');
         // ->orderBy(DB::raw("FIELD(medical_term_article_link.medical_term_id,'3225')"), $sort);
         // ->orderByRaw("FIELD(medical_term_article_link.medical_term_id,{$id}) {$sort}");
      }
   }
}
