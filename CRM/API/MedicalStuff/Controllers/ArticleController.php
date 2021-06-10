<?php

namespace CRM\API\MedicalStuff\Controllers;

use App\Helpers\Algolia;
use App\Http\Controllers\Controller;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\ItemType;
use CRM\API\MedicalStuff\MedicalStuffRepository;
use App\Http\Requests\Backend\MedicalStuff\Article\StoreArticleRequest;
use CRM\API\MedicalStuff\Traits\ArticleTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
   use ArticleTrait;

   private $model;

   protected $termRepo;

   public function __construct(MedicalArticle $model, MedicalStuffRepository $termRepo)
   {
      $this->model = $model;

      $this->termRepo = $termRepo;
   }

   public function get_active_paginated(Request $request)
   {
      $articles = $this->model
         ->select('medical_articles.*')
         ->active()
         ->when($request->filter, function ($query) {
            $query->filtered();
         })
         ->when($request->sortbyLang, function ($query) use ($request) {
            $request['locale'] = $request->sortbyLang;
            $query->sortedByNoLang($request->sortbyLang);
         })
         ->when($request->sortByTerm, function ($query) use ($request) {
            $query->sortedByTerm($request->term_id);
         })



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
         ->orderBy('created_at', 'Desc')
         ->paginate($request->perPage);

      return response()->json($articles);
   }

   public function index(Request $request)
   {
      $request['locale'] = $request->sortbyLang ?? $request->sortByGeolocalization ?? $request->locale;

      $articles = $this->model
         ->select('medical_articles.*')
         ->when($request->filter, function ($query) use ($request) {
            $query
               ->where('title', 'LIKE', "%{$request->filter}%");
         })
         ->active()
         ->with([
            'brands',
            'medical_terms',
            'type_dates',
            'author_slot',
            'image_slot',
            'geolocalization_template',
            'publishing_item_type_articles'
         ])
         ->when($request->articles != null, function ($q) use ($request) {
            $articleKeywords = ['Articles', 'Article', 'Articoli', 'Articolo'];
            $itemType = ItemType::query();
            foreach ($articleKeywords as $word) {
               $itemType->orWhere('type_name', 'LIKE', '%' . $word . '%');
            }
            $itemTypeName = $itemType->first()->type_name;
            return $q->where(function ($subQuery) use ($request, $itemTypeName) {
               $subQuery->whereHas('publishing_item_type_articles', function ($query) use ($request, $itemTypeName) {
                  $query->where('type_name', $itemTypeName);
               });
            });
         })
         ->withCount(['geolocalizations'])
         ->get();


      if ($request->sortByGeolocalization) {
         $articles = $this->termRepo->setSortByGeolocalization($articles, $request->sortByGeolocalization);
         return response()->json($articles);
      }

      if (!$request->sortbyLang) {
         return response()->json($articles);
      }

      $articles = $this->termRepo->setSortByLang($articles, $request->sortbyLang);

      return response()->json($articles);
   }

   public function get_active_paginated_articles(Request $request)
   {
      $articles = $this->model
         ->select('medical_articles.*')
         ->when($request->searchTopic, function ($query, $request) {
            $query->searchByTopic();
         })
         ->when($request->sortbyLang, function ($query) use ($request) {
            $request['locale'] = $request->sortbyLang;
            $query->sortedByNoLang($request->sortbyLang);
         })
         ->when($request->filterByGeolocalization, function ($query) use ($request) {
            $query->filteredByGeolocalization($request->term_id);
         })
         ->when($request->sortName, function ($query) use ($request) {
            $query->sortedByName($request->sortName, $request->sortDesc);
         })
         ->active()
         ->articles()
         ->with([
            'brands',
            'medical_terms',
            'type_dates',
            'author_slot',
            'image_slot',
            'geolocalization_template',
            'publishing_item_type_articles'
         ])
         ->withCount(['geolocalizations'])
         ->orderBy('created_at', 'Desc')
         ->paginate($request->perPage);


      return response()->json($articles);
   }

   public function show($id, Request $request)
   {
      $article = $this
         ->model
         ->select('medical_articles.*')
         ->active()
         ->with([
            'medical_terms',
            'brands',
            'type_dates',
            'author_slot',
            'image_slot',
            'publishing_item_type_articles',
            'course_info',
            'course_information_types.information_type',
            'course_term_descipline',
            'course_term_recepient'
         ])
         ->find($id);

      if (!$article) {
         throw ValidationException::withMessages(['name' => "No Publishing item found"]);
         return;
      }

      return response()->json([
         'article' => $article,
         'linked_terms' => $article->article_terms(),
      ]);
   }

   public function store(StoreArticleRequest $request)
   {
      // $rules = ['title' => 'required'];
      // if($request->images != null) {
      //     foreach ($request->images as $image) {
      //         dd(json_decode($request->tags, true));
      //     }
      //     for($x=0; $request->images >14; $x++) {
      //         $rules['radio_'. $x] = 'required';
      //     }
      // }

      // $messages = ['title.required' => 'The :attribute is required.'];
      // $request->validate($rules, $messages);

      // if ($request->actors) {
      //    $actorIdArray = collect($request->actors)->map(function ($actor) {
      //       return $actor['id'];
      //    });
      // }
      // dd($request->all());




      $article = '';

      MedicalArticle::withoutSyncingToSearch(function () use ($request, &$article) {
         $actorIdArray = [];

         if ($request->action === 'Add') {
            $this->termRepo->check_name_duplicate(
               [
                  'key' => 'title',
                  'value' => $request->title,
                  'locale' => $request->locale,
                  'brand_id' => $request->brand_id,
               ],
               $this->model->select('medical_articles.*')->get(),
               'articles'
            );

            $article =  $this->model->create([
               'title' => to_json_add($request->locale, $request->title),
               'link_url' => $request->link != null ? to_json_add($request->locale, $request->link) : NULL,
               'authors' => !empty($actorIdArray) ? json_encode(['actors' => $actorIdArray]) : null,
               'publish_date' => $request->publish_date
            ]);

            //Create Sync Reference for algolia on selected courses. 
            //Applicable for Courses Only 
            $this->checkItemTypeForSyncing(json_decode($request->item_types), $article->id);
         } else {
            $article =  $this->model->findOrFail($request->id);
            $article->title = to_json_add($request->locale, $request->title, to_json_remove($request->locale, $article->title));
            $article->link_url = ($request->link == null ? NULL : to_json_add($request->locale, $request->link, to_json_remove($request->locale, $article->link_url)));
            $article->publish_date = $request->publish_date;
            $article->authors = !empty($actorIdArray) ? json_encode(['actors' => $actorIdArray]) : null;
            $article->save();

            $this->termRepo->update_geolocalization_sync_reference($article);

            //catch changes on course types (medical_articles)
            $this->checkItemTypeForSyncing(json_decode($request->item_types), $article);
         }

         if (!empty(json_decode($request->item_types, true)) && $article) {

            //process changes on course types.
            $this->store_item_type_link($article->id,  json_decode($request->item_types, true));

            $courseForms = json_decode($request->course_form) ?: [];
            $inforTypeForm = json_decode($request->information_type_form) ?: [];

            // if ($request->action === 'Update') {
            //    if (empty($courseForms)) {
            //       $article->course_info()->delete();
            //    }
            //    if (empty($inforTypeForm)) {
            //       $article->course_information_types()->delete();
            //    }
            // }



            if (!empty($courseForms)) {
               $this->store_course_info($article, $courseForms);
            }

            if (!empty($inforTypeForm)) {
               $this->store_course_other_info($article, $inforTypeForm);
            }
         }

         if (!empty(json_decode($request->type_dates, true)) && $article) {
            $this->termRepo->store_article_date($article->id, json_decode($request->type_dates, true));
         }

         if (!empty(json_decode($request->actors, true)) && $article) {
            $this->termRepo->store_author_slot($article->id, json_decode($request->actors, true));
         }

         if (!empty($request->images) && $article) {
            $this->termRepo->store_image_slot($article->id, $request->images, json_decode($request->tags, true), $request->locale);
         }
      });

      return response()->json($article);
   }

   public function destroy(Request $request)
   {
      $article =  $this->model->findOrFail($request->id);

      if ($article) {
         $this->termRepo->remove_from_brand('brand_articles', 'article_id', $article->id);
         $this->termRepo->remove_from_sync_reference('sync_reference', 'table_id', $article->id);
      }

      $article->delete();

      return response()->json(true);
   }

   public function link_article_term(Request $request)
   {

      $medicalArticle = DB::table('medical_term_article_link');

      if ($request->key === 'link') {

         $term = $this->termRepo->findById($request->medical_term_id);

         if (!$term->has_term_types) {
            throw ValidationException::withMessages(['name' => "No Term types"]);
         }

         $medicalArticle
            ->insert([
               'medical_article_id' => $request->medical_article_id,
               'medical_term_id' => $request->medical_term_id,
               'created_at' => now(),
            ]);

         $this->termRepo->update_geolocalization_sync_reference_for_link_term($request->medical_article_id, $request->medical_term_id);
      } else {

         $this->termRepo->update_geolocalization_sync_reference_for_link_term($request->medical_article_id, $request->medical_term_id);

         $medicalArticle
            ->where('medical_article_id', $request->medical_article_id)
            ->where('medical_term_id', $request->medical_term_id)
            ->delete();
      }

      return response()->json(true);
   }

   public function get_article_terms_paginate(Request $request)
   {
      $result = $this->termRepo
         ->model()
         ->select('medical_terms.*')
         ->join('brand_terms', 'brand_terms.terminology_id', '=', 'medical_terms.id')
         ->when($request->filter, function ($query) use ($request) {
            $query
               ->where('name', 'LIKE', "%{$request->filter}%");
         })
         ->where(function ($query) use ($request) {
            $query->where('brand_terms.brand_id', $request->brand_id);
         })
         // ->with(['medical_articles'])
         // ->groupBy('medical_terms.id')
         // ->orderBy('medical_term_article_link.id', 'desc')
         ->paginate($request->per_page);

      // return    $result;
      $collections = collect($result->items())
         ->sortByDesc('medical_articles')
         ->values();

      return [
         'total' => $result->total(),
         "per_page" => $result->perPage(),
         "current_page" => $result->currentPage(),
         "last_page" => $result->lastPage(),
         "first_page_url" => $result->url(1),
         "last_page_url" => $result->lastPage(),
         "next_page_url" => $result->nextPageUrl(),
         "prev_page_url" => $result->previousPageUrl(),
         "from" => $result->currentPage(),
         "to" => (int)$result->perPage(),
         'path' => $result->path(),
         'data' =>  $collections,
      ];
   }

   public function update_image(Request $request)
   {
      $articleImageId = $this->termRepo->update_image($request->id, $request->image, $request->locale);

      return response()->json($articleImageId);
   }

   public function destroy_image(Request $request)
   {
      $result = $this->termRepo
         ->model()
         ->select('medical_terms.*')
         ->join('brand_terms', 'brand_terms.terminology_id', '=', 'medical_terms.id')
         ->when($request->filter, function ($query) use ($request) {
            $query
               ->where('name', 'LIKE', "%{$request->filter}%");
         })
         ->where(function ($query) use ($request) {
            $query->where('brand_terms.brand_id', $request->brand_id);
         })
         // ->with(['medical_articles'])
         // ->groupBy('medical_terms.id')
         // ->orderBy('medical_term_article_link.id', 'desc')
         ->paginate($request->per_page);

      // return    $result;
      $collections = collect($result->items())
         ->sortByDesc('medical_articles')
         ->values();

      return [
         'total' => $result->total(),
         "per_page" => $result->perPage(),
         "current_page" => $result->currentPage(),
         "last_page" => $result->lastPage(),
         "first_page_url" => $result->url(1),
         "last_page_url" => $result->lastPage(),
         "next_page_url" => $result->nextPageUrl(),
         "prev_page_url" => $result->previousPageUrl(),
         "from" => $result->currentPage(),
         "to" => (int)$result->perPage(),
         'path' => $result->path(),
         'data' =>  $collections,
      ];
   }

   public function store_publish_content(Request $request)
   {
      $article = $this->model->find($request->article_id);

      if ($article->publishing_item_content()->count()) {
         $response = $article->publishing_item_content->update([
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'slug' => $request->slug,
            'alt_tag_image' => $request->alt_tag_image,
            'img_description' => $request->image_description,
         ]);
      } else {
         $response = $article->publishing_item_content()->create([
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'slug' => $request->slug,
            'alt_tag_image' => $request->alt_tag_image,
            'img_description' => $request->image_description,
         ]);
      }

      //Algolia Geolocalization
      if ($article->geolocalization_sync_reference != null) {
         $article->geolocalization_sync_reference()->updateOrCreate([
            'table_id'   => $article->id,
         ], [
            'sync_class' => 'Geolocalization',
            'action' => isset($article->geolocalization_sync_reference->action) ? $article->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
            'source_table' => 'geolocalizations',
         ]);
      };

      //Algolia Sync Update if Course Type
      $this->termRepo->update_course_sync_reference($article);

      return response()->json($response);
   }

   public function autosave_publish_content(Request $request)
   {
      $article = $this->model->find($request->article_id);

      if ($article->publishing_item_content()->count()) {
         $response = $article->publishing_item_content->update([
            'content' => $request->content,
         ]);
      } else {
         $response = $article->publishing_item_content()->create([
            'content' => $request->content,
         ]);
      }

      return response()->json($response);
   }
}
