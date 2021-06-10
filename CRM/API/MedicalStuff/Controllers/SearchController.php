<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\MedicalStuff\MedicalTerm;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use CRM\API\MedicalStuff\Traits\ItemsTrait;
use CRM\API\MedicalStuff\Traits\SearchTrait;
use Illuminate\Http\Request;

class SearchController extends Controller
{
   use SearchTrait;
   use ItemsTrait;

   private $model;

   private $termRepo;

   public function __construct(MedicalTerm $model, MedicalStuffRepository $termRepo)
   {
      $this->model = $model;

      $this->termRepo = $termRepo;
   }

   public function advance_search_terms(Request $request)
   {
      $paginated = $this->model
         ->select('medical_terms.*')
         ->active()
         // ->when($request->specializations, function ($query) {
         //    $query->checkBySpecilization();
         // })
         // ->when($request->terms, function ($query) {
         //    $query->checkByTerm();
         // })
         // ->when($request->type_terms, function ($query) {
         //    $query->checkByTypeTerm();
         // })
         // ->when($request->articles, function ($query) {
         //    $query->checkByArticle();
         // })
         ->with([
            'medical_articles',
            'brands',
            'service'
         ])
         ->paginate($request->perPage);

      return response()->json($paginated);
   }

   public function advance_search_article(Request $request)
   {
      $results = MedicalArticle::select('medical_articles.*')
         ->active()
         ->when($request->terms, function ($query) {
            $query->checkTerms();
         })
         ->when($request->item_types, function ($query) {
            $query->checkItemTypes();
         })
         ->when($request->type_dates, function ($query) {
            $query->checkTypeDate();
         })
         ->when($request->authors, function ($query) {
            $query->checkAuthors();
         })
         ->with([
            'brands',
            'medical_terms',
            'type_dates',
            'author_slot',
            'image_slot',
            'geolocalization_template',
            'publishing_item_type_articles',
            'publishing_item_content',
            'course_info',
            'course_information_types.information_type'
         ])
         ->withCount(['geolocalizations'])
         ->orderBy('created_at', 'desc')
         ->paginate();

      return $results;
   }
}
