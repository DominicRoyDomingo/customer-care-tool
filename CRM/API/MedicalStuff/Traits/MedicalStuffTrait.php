<?php

namespace CRM\API\MedicalStuff\Traits;

use App\Helpers\Algolia;
use App\Helpers\General\ImageHelper;
use App\Models\Brand;
use App\Models\MedicalStuff\TermConnectionDescription;
use App\Models\MedicalStuff\TermDescription;
use App\Models\MedicalStuff\ArticleImage;
use App\Models\MedicalStuff\Icon;
use App\Models\MedicalStuff\MedicalTermType;
use App\Models\Service;
use App\Models\ServicesBrand;
use App\Models\Provider;
use App\Models\ProviderService;
use App\Models\ArticleContentMaker\Geolocalization;
use DB;
use Exception;
use Illuminate\Validation\ValidationException;

/**
 * For Medical Stuff Cotnroller
 */
trait MedicalStuffTrait
{
   public function check_name_duplicate($array,  $items, $type)
   {
      $item = get_data_by_name($array['key'], $array['value'], get_data($array['locale'], [$array['key'], 'id'], $items));

      $data = __('menus.backend.sidebar.type_of_terms');
      $brand_table = 'brand_term_types';
      $brand_table_field = 'term_type_id';

      if ($type === 'terms') {
         $data = __('menus.backend.sidebar.terminilogies');
         $brand_table = 'brand_terms';
         $brand_table_field = 'terminology_id';
      } else if ($type === 'articles') {
         $data = __('menus.backend.sidebar.articles');
         $brand_table = 'brand_articles';
         $brand_table_field = 'article_id';
      } else if ($type === 'type_dates') {
         $data = __('menus.backend.sidebar.type_dates');
      } else if ($type === 'term_descriptions') {
         $data = __('menus.backend.sidebar.descriptions');
      } else if ($type === 'term_connection_descriptions') {
         $data = __('menus.backend.sidebar.descriptions');
      }

      if (!is_null($item)) {

         if ($type === 'type_dates' || $type === 'term_descriptions' || $type === 'term_connection_descriptions') {
            throw ValidationException::withMessages([$array['key'] => __('validation.exist', ['attribute' => $array['value'], 'data' => $data])]);
         }

         $message = '';
         DB::beginTransaction();

         if (DB::table($brand_table)->where($brand_table_field, $item['id'])->where('brand_id', $array['brand_id'])->count() > 0) {
            $message = __('validation.exist', ['attribute' => $array['value'], 'data' => $data]);
         } else {

            $brandType = DB::table($brand_table)->where($brand_table_field, $item['id'])->pluck('brand_id');
            $brandName = '';
            $i = 1;
            foreach ($brandType as $brandId) {
               $brand = Brand::findOrFail($brandId);
               $brandName .= strtoupper($brand->brand_name);
               if ($i < count($brandType)) {
                  $brandName .= ', ';
                  $i++;
               }
            }

            $message = __('validation.exist_other_brand', [
               'attribute' => $array['value'],
               'data' => $data,
               'brand_name' => $brandName
            ]);
         }
         DB::commit();

         throw ValidationException::withMessages([$array['key'] => $message]);
      }
   }

   public function get_provider_types()
   {

      $providerTypeId = MedicalTermType::where('name', 'like', '%Provider%')->first()->id;

      $providerTypeTerms = $this->model->where('term_types->term_types', 'like', '%' . $providerTypeId . '%')->get();

      return $providerTypeTerms;
   }

   public function get_professional_terms()
   {

      $professionalKeywords = ['Professional', 'Professionals', 'Professionista'];

      $professionalTypeID = MedicalTermType::whereIn('name->en', $professionalKeywords)
      ->orWhereIn('name->de', $professionalKeywords)
      ->orWhereIn('name->it', $professionalKeywords)
      ->orWhereIn('name->ph-bis', $professionalKeywords)
      ->orWhereIn('name->ph-fil', $professionalKeywords)->first()->id;

      $professionalTerms = $this->model->where('term_types->term_types', 'like', '%' . $professionalTypeID . '%')->get();

      return $professionalTerms;
   }

   public function get_category_services()
   {
      $categoryServiceNames = [
         'Category of Service',
         'Category of Services',
         'Categories of Service',
         'Service Categories',
         'Service Category',
         'Categoria di Servizio',
         'Categoria di Servizi',
         'Categorie di Servizio',
      ];

      $medicalTermTypeId = MedicalTermType::where(function ($query) use ($categoryServiceNames) {
         foreach ($categoryServiceNames as $categoryServiceName) {
            $query->orWhere('name', 'LIKE', "%$categoryServiceName%");
         }
      })->first()->id;

      $medicalTerms = $this->model->whereRaw("JSON_CONTAINS(JSON_EXTRACT(term_types, '$.term_types'), '{$medicalTermTypeId}')")->get();

      return $medicalTerms;
   }

   public function get_services_by_category_service($term)
   {
      $idArrray  = json_decode($term->medical_terms);

      $serviceNames = [
         'Service',
         'Servizio',
         'Servizi',
         'Serbisyo',
      ];

      $termItems = collect($idArrray)
         ->map(function ($id) {
            return $this->getById($id);
         });

      $filteredTerms = $termItems->filter(function ($term) use ($serviceNames) {
         return collect($term->has_term_types)->filter(function ($term) use ($serviceNames) {
            $language = $this->get_available_language($term->name, locale());
            $name = json_decode($term->name)->$language;
            return in_array($name, $serviceNames, true);
         })->count() > 0;
      })->values();

      return $filteredTerms;
   }


   public function get_paginated_services_terms($request)
   {

      $search = $request->search;

      $serviceNames = [
         'Service',
         'Servizio',
         'Servizi',
         'Serbisyo',
      ];

      $serviceId = MedicalTermType::query();
      foreach ($serviceNames as $service) {
         $serviceId->orWhere('name', 'LIKE', '%' . $service . '%');
      }
      $serviceId = $serviceId->first()->id;

      $perPage = strlen($request->filter) > 0 ? $this->total_active_terms() : $request->perPage;

      if (empty($search)) {
         $paginate = $this->model
            ->active()
            ->whereRaw("JSON_CONTAINS(JSON_EXTRACT(term_types, '$.term_types'), '{$serviceId}')")
            ->with([
               'medical_articles',
               'brands',
               'service'
            ])->paginate($perPage);
      } else {
         $paginate = $this->model
            ->active()
            ->whereRaw("JSON_CONTAINS(JSON_EXTRACT(term_types, '$.term_types'), '{$serviceId}') and (name like '%" . $search . "%')")
            ->with([
               'medical_articles',
               'brands',
               'service'
            ])->paginate($perPage);
      }


      if ($request->places != "") {
         $items = $paginate->getCollection()->transform(function ($term, $key) use ($request) {
            $medicalArticlesId = array();
            foreach ($term->medical_articles as $article) {
               array_push($medicalArticlesId, $article->id);
            }
            $places = array();
            foreach ($request->places as $place) {
               $medicalArticle = DB::table('medical_term_article_link as mtl')
                  ->leftJoin('provider_services as ps', 'ps.services', 'mtl.medical_term_id')
                  ->leftJoin('providers as p', 'p.id', 'ps.providers')
                  ->select(['p.id as providers_id', 'p.name as providers_name', 'p.address as providers_address'])
                  ->whereIn('mtl.medical_article_id', $medicalArticlesId)
                  ->where('p.country', '=', json_decode($place, true)['country_id'])
                  // -> where('p.city', '=', json_decode($place, true)['city_id'] )
                  ->when(isset(json_decode($place, true)['city_id']), function ($q) use ($place) {
                     return $q->where('p.city', '=', json_decode($place, true)['city_id']);
                  })
                  ->where('p.division', '=', json_decode($place, true)['division_id'])
                  ->distinct();
               $provider_list = [];

               foreach ($medicalArticle->get() as $prov) {

                  $provider_list[] = [
                     'id' => $prov->providers_id,
                     'name' => getTranslation($prov->providers_name, $request->locale),
                     'address' => $prov->providers_address,
                     // 'services' => $services_list
                  ];
               }
               $places[json_decode($place, true)['place']] = [
                  'count_providers'    => COUNT($provider_list),
                  'providers'    =>  $provider_list,
               ];
            }


            $term['places'] = $places;
            return $term;
         });
         // $paginate = $paginate->getCollection()->transform(function ($term) use($request, $countries, $provinces, $cities) {

      } else {
         $items = $paginate->getCollection();
      }

      if (strlen($request->filter) > 0) {
         $items = $this->get_filter_items($paginate);
      }

      return $paginate->setCollection($items);
   }

   public function get_linked_medical_terms(array $data)
   {
      $items = [];
      foreach ($data as $id) {
         $items[] = $this->with(['medical_articles'])->getById($id);
      }
      return $items;
   }

   public function linked_term_description(int $parentId, int $childId)
   {
      return  TermConnectionDescription::select('term_connection_descriptions.*')
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
      // if (!empty($termConDesc)) {
      //    foreach ($termConDesc as  $desc) {
      //       $items[] = $desc;
      //    }
      // }

      // return   $items;
   }

   // public function store_link_description(array $data)
   // {
   //    // $termDesc = new TermConnectionDescription();
   //    return TermConnectionDescription::create([
   //       'term_id' => $data['term_id'],
   //       'linked_term_id' => $data['linked_term_id'],
   //       'description_id' => $data['description_id'],
   //       'value' => $data['value'],
   //       'note' => $data['note'],
   //    ]);
   // }

   public function remove_term_description($parentId,  $childId)
   {
      $desc = TermConnectionDescription::where('term_id',  $parentId)
         ->where('linked_term_id',  $childId);

      if ($desc->count() > 0) {
         return $desc->delete();
      }
   }

   public function remove_medical_term(array $data, $parentId)
   {
      foreach ($data as $id) {
         $child = $this->getById($id);
         $childMT = json_decode($child->medical_terms) ?? [];

         foreach (array_keys($childMT, $parentId, true) as $key) {
            unset($childMT[$key]);
         }
         $child->medical_terms = !empty(array_values($childMT)) ? json_encode(array_values($childMT)) : null;
         $child->save();
      }
   }

   public function remove_from_brand($table, $field, $id)
   {
      DB::beginTransaction();
      try {
         $brandType = DB::table($table)->where($field, $id);
         if ($brandType->count() > 0) {
            $brandType->delete();
         }
         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }

   public function remove_from_sync_reference($table, $field, $id)
   {
      DB::beginTransaction();
      try {
         $geolocalizationSyncReference = DB::table($table)->where($field, $id)->where('source_table', 'geolocalizations');
         if ($geolocalizationSyncReference->count() > 0) {
            $geolocalizationSyncReference->delete();
         }
         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
      }
   }

   public function store_article_date($id, array $data)
   {
      $artcleTbl = DB::table('article_dates');

      if ($artcleTbl->where('article_id', $id)->count() > 0) {
         $artcleTbl->where('article_id', $id)->delete();
      }

      foreach ($data as $value) {
         if ($value['date'] != null) {
            $artcleTbl->insert([
               'article_id' => $id,
               'type_date_id' => $value['id'],
               'article_date' => $value['date'],
               'created_at' => now(),
            ]);
         }
      }
   }

   public function store_author_slot($id, array $data)
   {
      $artcleAuthorTbl = DB::table('article_authors');

      if ($artcleAuthorTbl->where('article', $id)->count() > 0) {
         $artcleAuthorTbl->where('article', $id)->delete();
      }


      foreach ($data as $value) {
         if ($value['actors'] != null && !empty($value['actors'])) {
            $array_actor = [];
            foreach ($value['actors'] as $actors) {
               array_push($array_actor, $actors['id']);
            }

            $artcleAuthorTbl->insert([
               'article' => $id,
               'author_type' => $value['actor_type'],
               'authors' => json_encode($array_actor)
            ]);
         }
      }
   }

   public function store_image_slot($id, array $data, array $tags, $lang)
   {
      $request = request();
      if (ArticleImage::where('article', $id)->count() > 0) {
         foreach ($data as $key => $value) {

            if ($tags['id'][$key] != null) {
               $article = ArticleImage::findOrFail($tags['id'][$key]);
               $article->html_tags = count($tags['tagsId'][$key]) != 0 ? json_encode($tags['tagsId'][$key]) : null;
               $article->update();
               if ($value != 'null') $this->upload_image($article, $value, $lang);
            } else if ($value != 'null') {
               $articleImage = ArticleImage::create([
                  'article'   => $id,
                  'html_tags' => count($tags['tagsId'][$key]) != 0 ? json_encode($tags['tagsId'][$key]) : null
               ]);
               array_push($tags['id'], $articleImage->id);
               $this->upload_image($articleImage, $value, $lang);
            }
         }
         $articleImages = ArticleImage::where('article', $id)->pluck('id')->toArray();
         $toBeDeleted = array_diff($articleImages, $tags['id']);
         if (count($toBeDeleted) != 0) {
            ArticleImage::whereIn('id', $toBeDeleted)->delete();
            $this->remove_existing_image($toBeDeleted, $articleImages);
         }
         return;
      }


      foreach ($data as $key => $value) {
         $articleImage = ArticleImage::create([
            'article'   => $id,
            'html_tags' => count($tags['tagsId'][$key]) != 0 ? json_encode($tags['tagsId'][$key]) : null
         ]);

         $this->upload_image($articleImage, $value, $lang);
      }
   }

   public function update_image($id, $image, $lang)
   {
      $request = request();
      if ($image == 'null') return $id;
      $article = ArticleImage::findOrFail($id);
      $this->upload_image($article, $image, $lang);

      return $id;
   }

   public function upload_image($data, $file, $lang = null)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $data->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.medical_term'),
         'file' => $file,
      ]);
      $image->deleteDirectory();

      if ($file && $image->upload()) {

         $imageURL = !is_null($lang) ? json_encode([$lang => $image->imageUrl()]) : $image->imageUrl();

         $data->image = $imageURL;
         $data->save();
      }
   }

   public function upload_icon($data, $file, $lang = null)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $data->id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.icon'),
         'file' => $file,
      ]);

      $image->deleteDirectory();

      if ($file && $image->upload()) {

         $iconURL = !is_null($lang) ? json_encode([$lang => $image->imageUrl()]) : $image->imageUrl();
         $data->icon_url = $iconURL;
         $data->save();
      }
   }

   public function store_service($data): void
   {
      $locale  = request()->locale;

      $serviceByData = get_data_by_name('name', $data->term_name, get_data($locale, ['id', 'name'],  Service::get()));

      $service = $serviceByData ? (object)$serviceByData :  Service::where('medical_term', $data->term_id)->first();

      if (!$service) {
         DB::beginTransaction();
         try {
            $service = DB::table('services')
               ->insertGetId([
                  'name' => to_json_add($locale, $data->term_name),
                  'images' => $data->term_img_url,
                  'created_by' => request()->user()->id,
                  'medical_term' => $data->term_id,
               ]);
            DB::commit();
         } catch (\Exception $e) {
            //throw $e;
            DB::rollBack();
         }
      }

      $this->store_service_brand($service);
   }

   public function store_service_brand($service)
   {
      $brandId   = request()->brand_id;
      $serviceId = is_int($service) ? $service : $service->id;

      $serviceBrand = ServicesBrand::where('service', $serviceId)->where('brands', $brandId);

      if ($serviceBrand->count() > 0) {
         $serviceBrand->delete();
      }

      ServicesBrand::create([
         'service' =>  $serviceId,
         'brands'  =>  $brandId,
         'created_at' => now(),
      ]);
   }

   public function store_icon($request)
   {
      $icon = Icon::create([
         'term' => $request->term,
         'provider_type' => $request->provider_type,
      ]);

      $this->upload_icon($icon, $request->icon);

      $this->update_provider_sync_reference($icon->medical_term);
   }

   public function update_geolocalization_sync_reference($article)
   {
      if ($article->geolocalization_sync_reference != null) {
         $article->geolocalization_sync_reference()->updateOrCreate([
            'table_id'   => $article->id,
         ],[
            'sync_class' => 'Geolocalization',
            'action' => $article->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'geolocalizations',
         ]);
      }
   }

   public function update_course_sync_reference($article)
   {
      if ($article->course_sync_reference != null) {
         $article->course_sync_reference()->update([
            'action' => 'Update'
         ]);
      }
   }

   public function update_provider_sync_reference($term)
   {

      $categoryServiceNames = [
         'Category of Service',
         'Category of Services',
         'Categories of Service',
         'Service Categories',
         'Service Category',
         'Categoria di Servizio',
         'Categoria di Servizi',
         'Categorie di Servizio',
      ];

      if (collect($term->has_term_types)->contains(function ($value) use ($categoryServiceNames) {
         $language = $this->get_available_language($value->name, locale());
         $name = json_decode($value->name)->$language;
         return in_array($name, $categoryServiceNames, true);
      })) {
         $terms = json_decode($term->medical_terms);

         $providerIds = ProviderService::whereIn('services', $terms)->pluck('providers')->unique()->values();

         $providers = Provider::whereIn('id', $providerIds)->get();

         foreach ($providers as $provider) {
            if ($provider->provider_sync_reference != null) {
               $provider->provider_sync_reference()->updateOrCreate([
                  'table_id'   => $provider->id,
               ], [
                  'sync_class' => 'Provider',
                  'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
                  'source_table' => 'providers',
               ]);
            }
         }
         return;
      }

      if (!$term->provider_services->isEmpty()) {
         foreach ($term->provider_services as $service) {
            if ($service->provider_item->provider_sync_reference != null) {
               $service->provider_item->provider_sync_reference()->updateOrCreate([
                  'table_id'   => $service->provider_item->id,
               ], [
                  'sync_class' => 'Provider',
                  'action' => $service->provider_item->provider_sync_reference->action != 'New' ? 'Update' : 'New',
                  'source_table' => 'providers',
               ]);
            }
         }
      }
   }

   public function remove_icon($request)
   {
      $icon = Icon::findOrFail($request->id);

      $this->update_provider_sync_reference($icon->medical_term);

      $icon->delete();

      $this->remove_existing_icon($icon->id);

      return true;
   }



   public function remove_existing_image($data, $file)
   {
      foreach ($data as $id) {
         $image = new ImageHelper([
            'driver' => 's3',
            'id' => $id,
            's3_storage_path' => 'customer-care-tool',
            's3_folder_path' => config('access.s3_path.medical_term'),
            'file' => $file,
         ]);
         $image->deleteDirectory();
      }
   }

   public function remove_existing_icon($id, $file = 'test')
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => 'term-icons',
         'file' => $file,
      ]);
      $image->deleteDirectory();
   }

   public function remove_service(int $id)
   {
      $service = Service::where('medical_term', $id);

      if ($service->count() == 0) {
         return false;
      }

      $service->delete();

      return true;
   }

   public function set_term_valueId(array $data, $childId, $key)
   {
      $value = '';
      if ($key == 'link') {
         $value = !in_array($childId, $data) ? json_encode(array_merge($data, [$childId])) : json_encode($data);
      } else {
         foreach (array_keys($data, $childId, true) as $key) {
            unset($data[$key]);
         }

         $value = !empty(array_values($data)) ? json_encode(array_values($data)) : null;
      }

      return $value;
   }

   public function get_available_language($name, $lang)
   {
      $availableLanguage = array_key_first(json_decode($name, true));

      return isset(json_decode($name)->$lang) ? $lang : $availableLanguage;
   }
}
