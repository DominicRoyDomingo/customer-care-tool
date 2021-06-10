<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\MedicalArticle;
use CRM\API\MedicalStuff\Traits\SearchTrait;
use Illuminate\Http\Request;

class SearchArticleController extends Controller
{
   use SearchTrait;

   private $model;

   public function __construct(MedicalArticle $model)
   {
      $this->model = $model;
   }

   public function __invoke(Request $request)
   {
      $results = $this->model
         ->select('medical_articles.*')
         ->join('brand_articles', 'brand_articles.article_id', '=', 'medical_articles.id')
         ->where('brand_articles.brand_id', $request->brand_id)
         ->with([
            'brands',
            'medical_terms',
            'type_dates',
            'author_slot',
            'image_slot',
            'geolocalization_template'
         ])
         ->withCount(['geolocalizations'])
         ->get();
      $terms = collect($results)
         ->map(function ($term) use ($request) {

            if ($request->authors && $request->terms && $request->type_dates) {
               if (
                  $this->check_author_article($term->author_slot, $request->authors)
                  &&
                  $this->check_terms_for_article(json_decode($term->medical_terms), $request->terms)
                  &&
                  $this->check_type_date_article($term->type_dates, $request)
               ) {
                  return $term;
               }
            } else if ($request->authors && empty($request->terms) && empty($request->type_dates)) {
               if ($this->check_author_article($term->author_slot, $request->authors)) {
                  return $term;
               }
            } else if (empty($request->authors) && $request->terms && empty($request->type_dates)) {
               if ($this->check_terms_for_article($term->medical_terms, $request->terms)) {
                  return $term;
               }
            } else if (empty($request->authors) && empty($request->terms) && $request->type_dates) {
               if ($this->check_type_date_article($term->type_dates, $request)) {
                  return $term;
               }
            } else if ($request->authors && $request->terms && empty($request->type_dates)) {
               if ($this->check_author_article($term->author_slot, $request->authors) && $this->check_terms_for_article($term->medical_terms, $request->terms)) {
                  return $term;
               }
            } else if ($request->authors && empty($request->terms) && $request->type_dates) {
               if ($this->check_author_article($term->author_slot, $request->authors) && $this->check_type_date_article($term->type_dates, $request)) {
                  return $term;
               }
            } else if (empty($request->authors) && $request->terms && $request->type_dates) {
               if ($this->check_terms_for_article($term->medical_terms, $request->terms) && $this->check_type_date_article($term->type_dates, $request)) {
                  return $term;
               }
            } else {
               return $term;
            }
         })
         ->filter(function ($value) use ($request) {
            return $value;
         })
         ->values()
         ->all();
      return response()->json($terms);
   }
}
