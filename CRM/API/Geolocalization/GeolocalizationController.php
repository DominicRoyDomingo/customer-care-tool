<?php

namespace CRM\API\Geolocalization;

use Artisan;
use App\Http\Controllers\Controller;
use App\Models\ArticleContentMaker\Geolocalization;
use App\Models\ArticleContentMaker\GeolocalizeImage;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\SyncReference;
use App\Http\Requests\Backend\Geolocalization\StoreGeolocalizationRequest;
use App\Http\Requests\Backend\Geolocalization\PublishStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Helpers\General\ImageHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;

class GeolocalizationController extends Controller
{

   public function getCities(Request $request)
   {
      $lang = $request->lang;
      $filterBy = $request->filter_by;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $articleId = $request->article_id;
      $article = MedicalArticle::with('geolocalizations')->findOrFail($articleId);
      
      $geolocalizations = $article->geolocalizations->where('area', 'City');
      
      $geolocalizations = $geolocalizations->map(function ($geolocalization) use($lang){
         $article = DB::table( 'medical_articles' ) -> where( 'id', '=', $geolocalization->article ) -> select( 'title' ) -> first();
         
         $medicalArticle = DB::table('medical_term_article_link as mtl') 
                           -> leftJoin( 'provider_services as ps', 'ps.services', 'mtl.medical_term_id' )
                           -> leftJoin( 'providers as p', 'p.id', 'ps.providers' )
                           -> select( [ 'p.id as providers_id', 'p.name as providers_name' ] )
                           -> where('mtl.medical_article_id', '=', $geolocalization->article )
                           -> where('p.country', '=', $geolocalization->country )
                           -> where('p.city', '=', $geolocalization->city )
                           -> where('p.division', '=', $geolocalization->division )
                           -> distinct();
            $provider_list = [];
            
            foreach( $medicalArticle -> get() as $prov ) {
               
               $services = DB::table( 'provider_services' ) -> where( 'providers', '=', $prov -> providers_id ) -> get();
               
               $services_list = [];
               foreach( $services as $serv ) {
                  // $service = DB::table( 'medical_terms' ) -> where( 'id', '=', $serv -> services ) -> select('name') -> first();

                  $medical_link = DB::table('medical_term_article_link as mtal') 
                                 -> leftJoin('medical_terms as mt', 'mt.id', 'mtal.medical_term_id') 
                                 -> where('mtal.medical_article_id', '=', $geolocalization->article )
                                 -> where('mt.id', '=', $serv -> services )
                                 -> select( [ 'mt.id', 'mt.name' ] )
                                 -> get();
                  
                  foreach( $medical_link as $medical_link_list ) {
                     $services_list[] = [
                        'name' => getTranslation( $medical_link_list-> name, $lang)
                     ];
                  }
                  
               }

               $provider_list[] = [
                  'id' => $prov -> providers_id,
                  'name' => getTranslation($prov -> providers_name, $lang),
                  'services' => $services_list
               ];
            }

         $division = $geolocalization->division_item != null ? $geolocalization->division_item->name.', ' : "";
         return [
               'geolocalization_id' => $geolocalization->id,
               'article_id'         => $geolocalization->article,
               'place'              => $geolocalization->city_item->name,
               'country_id'         => $geolocalization->country,
               'division_id'        => $geolocalization->division,
               'city_id'            => $geolocalization->city,
               'geolocalize_images' => $geolocalization->geolocalize_images,
               'publish_status'     => $geolocalization->publish_status,
               'count_providers'    => COUNT( $provider_list ),
               'providers'    =>  $provider_list,
               'article' => getTranslation( $article -> title, $lang ) 
         ];
      });
      switch ($request->sort_name) {
         case 'name':
            $geolocalizations = $geolocalizations->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $geolocalizations = $geolocalizations->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $geolocalizations = $geolocalizations->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if($filterBy) {
         $geolocalizations = $geolocalizations
         ->filter(function ($item) use ($filterBy) {
            switch ($filterBy) {
               case 'wuga':
                  if ($item['publish_status'] == 'Unpublished') return $item;
                  break;
               case 'wpga':
                  if ($item['publish_status'] == 'Published') return $item;
                  break;
            }
         })
         ->values();
      }

      if($search != null) {

         $geolocalizations = $geolocalizations->filter(function($item) use ($search) {
            $place = str_replace(array(' ', ','), '' , $item['place']);
            $search = str_replace(array(' ', ','), '' , $search);
            
            return stripos($place, $search) !== false;
         });
         
      }

      $geolocalizations = $this->paginate($geolocalizations, $entries, $page);
      
      return response()->json($geolocalizations);
   }

   public function getProvinces(Request $request)
   {
      $lang = $request->lang;
      $filterBy = $request->filter_by;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $articleId = $request->article_id;

      $article = MedicalArticle::with('geolocalizations')->findOrFail($articleId);
      
      // $geolocalizations = ;

      // dd();
      $geolocalizations = $article->geolocalizations->where('area', 'Province');
      $geolocalizations = $geolocalizations->map(function ($geolocalization) use($lang){

         $article = DB::table( 'medical_articles' ) -> where( 'id', '=', $geolocalization->article ) -> select( 'title' ) -> first();

         $medicalArticle = DB::table('medical_term_article_link as mtl') 
                           -> leftJoin( 'provider_services as ps', 'ps.services', 'mtl.medical_term_id' )
                           -> leftJoin( 'providers as p', 'p.id', 'ps.providers' )
                           -> select( [ 'p.id as providers_id', 'p.name as providers_name' ] )
                           -> where('mtl.medical_article_id', '=', $geolocalization->article )
                           -> where('p.country', '=', $geolocalization->country )
                           -> where('p.division', '=', $geolocalization->division );

         $provider_list = [];
         foreach( $medicalArticle -> get() as $prov ) {

            $services = DB::table( 'provider_services' ) -> where( 'providers', '=', $prov -> providers_id ) -> get();
            
            $services_list = [];
            foreach( $services as $serv ) {
               // $service = DB::table( 'medical_terms' ) -> where( 'id', '=', $serv -> services ) -> select('name') -> first();

               $medical_link = DB::table('medical_term_article_link as mtal') 
                              -> leftJoin('medical_terms as mt', 'mt.id', 'mtal.medical_term_id') 
                              -> where('mtal.medical_article_id', '=', $geolocalization->article )
                              -> where('mt.id', '=', $serv -> services )
                              -> select( [ 'mt.id', 'mt.name' ] )
                              -> get();
               
               foreach( $medical_link as $medical_link_list ) {
                  $services_list[] = [
                     'name' => getTranslation( $medical_link_list-> name, $lang)
                  ];
               }
               
            }

            $provider_list[] = [
               'id' => $prov -> providers_id,
               'name' => getTranslation($prov -> providers_name, $lang),
               'services' => $services_list
            ];
         }

         $division = $geolocalization->division_item != null ? $geolocalization->division_item->name : "";
         return [
               'geolocalization_id' => $geolocalization->id,
               'article_id'         => $geolocalization->article,
               'place'        => $division,
               'country_id'   => $geolocalization->country,
               'division_id'  => $geolocalization->division,
               'geolocalize_images' => $geolocalization->geolocalize_images,
               'publish_status'     => $geolocalization->publish_status,
               'count_providers' => $medicalArticle -> count(),
               'providers'    =>  $provider_list,
               'article' => getTranslation( $article -> title, $lang ) 
         ];
      });
     
      if($filterBy) {
         $geolocalizations = $geolocalizations
         ->filter(function ($item) use ($filterBy) {
            switch ($filterBy) {
               case 'wuga':
                  if ($item['publish_status'] == 'Unpublished') return $item;
                  break;
               case 'wpga':
                  if ($item['publish_status'] == 'Published') return $item;
                  break;
            }
         })
         ->values();
      }
      
      switch ($request->sort_name) {
         case 'name':
            $geolocalizations = $geolocalizations->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $geolocalizations = $geolocalizations->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $geolocalizations = $geolocalizations->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if($search != null) {

         $geolocalizations = $geolocalizations->filter(function($item) use ($search) {
            $place = str_replace(array(' ', ','), '' , $item['place']);
            $search = str_replace(array(' ', ','), '' , $search);
            
            return stripos($place, $search) !== false;
         });
         
      }
      
      $geolocalizations = $this->paginate($geolocalizations, $entries, $page);
      
      return response()->json($geolocalizations);
   }

   public function getRegions(Request $request)
   {
      $lang = $request->lang;
      $filterBy = $request->filter_by;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $articleId = $request->article_id;

      $article = MedicalArticle::with('geolocalizations')->findOrFail($articleId);
      
      // $geolocalizations = ;

      // dd();
      $geolocalizations = $article->geolocalizations->where('area', 'Region');
      $geolocalizations = $geolocalizations->map(function ($geolocalization) use($lang){
         
         $article = DB::table( 'medical_articles' ) -> where( 'id', '=', $geolocalization->article ) -> select( 'title' ) -> first();

         // $medicalArticle = DB::table('medical_term_article_link as mtl') 
         //                   -> leftJoin( 'provider_services as ps', 'ps.services', 'mtl.medical_term_id' )
         //                   -> leftJoin( 'providers as p', 'p.id', 'ps.providers' )
         //                   -> select( [ 'p.id as providers_id', 'p.name as providers_name' ] )
         //                   -> where('mtl.medical_article_id', '=', $geolocalization->article )
         //                   -> where('p.country', '=', $geolocalization->country )
         //                   -> where('p.division', '=', $geolocalization->division );

         $provider_list = [];
         // foreach( $medicalArticle -> get() as $prov ) {

         //    $services = DB::table( 'provider_services' ) -> where( 'providers', '=', $prov -> providers_id ) -> get();
            
         //    $services_list = [];
         //    foreach( $services as $serv ) {
         //       // $service = DB::table( 'medical_terms' ) -> where( 'id', '=', $serv -> services ) -> select('name') -> first();

         //       $medical_link = DB::table('medical_term_article_link as mtal') 
         //                      -> leftJoin('medical_terms as mt', 'mt.id', 'mtal.medical_term_id') 
         //                      -> where('mtal.medical_article_id', '=', $geolocalization->article )
         //                      -> where('mt.id', '=', $serv -> services )
         //                      -> select( [ 'mt.id', 'mt.name' ] )
         //                      -> get();
               
         //       foreach( $medical_link as $medical_link_list ) {
         //          $services_list[] = [
         //             'name' => getTranslation( $medical_link_list-> name, $lang)
         //          ];
         //       }
               
         //    }

         //    $provider_list[] = [
         //       'id' => $prov -> providers_id,
         //       'name' => getTranslation($prov -> providers_name, $lang),
         //       'services' => $services_list
         //    ];
         // }

         $region = $geolocalization->region_item != null ? $geolocalization->region_item->region : "";
         return [
               'geolocalization_id' => $geolocalization->id,
               'article_id'         => $geolocalization->article,
               'place'        => $region,
               'country_id'   => $geolocalization->country,
               'region_id'    => $geolocalization->region,
               'geolocalize_images' => $geolocalization->geolocalize_images,
               'publish_status'     => $geolocalization->publish_status,
               'count_providers' => 0,
               'providers'    =>  $provider_list,
               'article' => getTranslation( $article -> title, $lang ) 
         ];
      });

      if($filterBy) {
         $geolocalizations = $geolocalizations
         ->filter(function ($item) use ($filterBy) {
            switch ($filterBy) {
               case 'wuga':
                  if ($item['publish_status'] == 'Unpublished') return $item;
                  break;
               case 'wpga':
                  if ($item['publish_status'] == 'Published') return $item;
                  break;
            }
         })
         ->values();
      }
      
      switch ($request->sort_name) {
         case 'name':
            $geolocalizations = $geolocalizations->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $geolocalizations = $geolocalizations->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $geolocalizations = $geolocalizations->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if($search != null) {

         $geolocalizations = $geolocalizations->filter(function($item) use ($search) {
            $place = str_replace(array(' ', ','), '' , $item['place']);
            $search = str_replace(array(' ', ','), '' , $search);
            
            return stripos($place, $search) !== false;
         });
         
      }

      
      $geolocalizations = $this->paginate($geolocalizations, $entries, $page);
      
      return response()->json($geolocalizations);
   }

   public function all(Request $request)
   {
      $lang = $request->lang;

      $serviceTypes = ServiceType::all()->map(function ($org) use($lang){

         $org['name'] = getTranslation($org['name'], $lang);
     
         return $org;
     
      });

      return response()->json($serviceTypes);
   }

   public function show(Request $request)
   {
      
      $article = MedicalArticle::findOrFail($request->article_id);

      return response()->json($article);
   }

   // for Category here
   public function store(StoreGeolocalizationRequest $request)
   {
      $lang = $request->lang;
      $articleId = $request->article_id;
      $geolocalization = 0;
      $article = MedicalArticle::with('geolocalizations')->findOrFail($articleId);
      Geolocalization::withoutSyncingToSearch(function () use($request, &$geolocalization, $article){
         if (is_null($request->id)) {
            foreach (json_decode($request->places, true) as $place) {
               $places = [
                  "country"  => $place['country_id'],
                  "area" => $request->area,
               ];
               
               switch ($request->area) {
                  case 'City':
                     $places['city'] = $place['city_id']; 
                     $places['division'] = $place['division_id'];
                     break;
                  case 'Province':
                     $places['division'] = $place['division_id'];
                     break;
                  case 'Region':
                     $places['regions'] = $place['region_id'];
                     break;
               };

               $geolocalizationCreated = $article->geolocalizations()->create($places);
               
               if($geolocalizationCreated->article_item->geolocalization_template != null) {
                  $geolocalizationCreated->article_item->geolocalization_sync_reference()->updateOrCreate([
                     'table_id'   => $geolocalizationCreated->article_item->id,
                  ],[
                     'sync_class' => 'Geolocalization',
                     'action' => isset($geolocalizationCreated->article_item->geolocalization_sync_reference->action) ? $geolocalizationCreated->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
                     'source_table' => 'geolocalizations',
                  ]);
               }
               
               // SyncReference::create([
               //    'sync_class' => 'Geolocalization',
               //    'action' => 'New',
               //    'source_table' => 'geolocalizations',
               //    'table_id' => $geolocalizationId,
               // ]);
            };
            

            $geolocalization = count(json_decode($request->places));
   
         } else {
   
            $place = json_decode($request->place, true);
            $geolocalization = Geolocalization::findOrFail($request->id);
            $geolocalization->country = $place['country_id'];
            switch ($request->area) {
               case 'City':
                  $geolocalization->city = $place['city_id'];
                  $geolocalization->division = $place['division_id'];
                  break;
               case 'Province':
                  $geolocalization->division = $place['division_id'];
                  break;
               case 'Region':
                  $geolocalization->regions = $place['region_id'];
                  break;
            };
            $geolocalization->update();

            if($geolocalization->article_item->geolocalization_template != null) {
               $geolocalization->article_item->geolocalization_sync_reference()->updateOrCreate([
                  'table_id'   => $geolocalization->article_item->id,
               ],[
                  'sync_class' => 'Geolocalization',
                  'action' => $geolocalization->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
                  'source_table' => 'geolocalizations',
               ]);
            }
            

            // if($geolocalization->geolocalization_sync_reference != null) {
   
            //    $geolocalization->geolocalization_sync_reference()->updateOrCreate([
            //       'table_id'   => $geolocalization->id,
            //    ],[
            //       'sync_class' => 'Geolocalization',
            //       'action' => $geolocalization->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
            //       'source_table' => 'geolocalizations',
            //    ]);
   
            // }
   
         }
        
         
      });
      

      return response()->json($geolocalization);
   }

   public function storeImage(Request $request)
   {
      $lang = $request->lang;
      $geolocalizationId = $request->id;
      $images = $request->images;
      if (is_null($request->geolocalize_image_id)) {
         
         foreach($images as $value) {
            $geolocalizeImage = GeolocalizeImage::create([
               'place'   => $geolocalizationId,
            ]);
            $this->uploadImages($geolocalizeImage, $value);
         }

      } else {

         $geolocalizeImage = GeolocalizeImage::findOrFail($request->geolocalize_image_id);
         $this->uploadImages($geolocalizeImage, $images);

      }
      $geolocalization = Geolocalization::findOrFail($geolocalizeImage->place);

      if($geolocalization->article_item->geolocalization_sync_reference != null) {

         $geolocalization->article_item->geolocalization_sync_reference()->updateOrCreate([
            'table_id'   => $geolocalization->article_item->id,
         ],[
            'sync_class' => 'Geolocalization',
            'action' => $geolocalization->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'geolocalizations',
         ]);

      }

      return response()->json(true);
   }

   public function changePublishStatus(PublishStatusRequest $request)
   {
      Geolocalization::withoutSyncingToSearch(function () use($request){
         $geolocalization = Geolocalization::findOrFail($request->id);
         $geolocalization->publish_status = $request->publish_status;
         $geolocalization->update();

         if($geolocalization->article_item->geolocalization_sync_reference != null) {

            $geolocalization->article_item->geolocalization_sync_reference()->updateOrCreate([
               'table_id'   => $geolocalization->article_item->id,
            ],[
               'sync_class' => 'Geolocalization',
               'action' => $geolocalization->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
               'source_table' => 'geolocalizations',
            ]);
   
         }
      });

      return response()->json(true);
   }

   public function getAlgoliaSummary( Request $request ) 
   {
      
      $newReferences = SyncReference::where('source_table', 'geolocalizations')->where('action', 'New')->count();
      $updateReferences = SyncReference::where('source_table', 'geolocalizations')->where('action', 'Update')->count();

      $summary = [
         'new' => $newReferences,
         'update' => $updateReferences,
      ];
      
      return response() -> json($summary);

   }

   public function syncToAlgolia(Request $request)
   {
      $medicalArticles = MedicalArticle::whereHas('geolocalization_sync_reference', function($q){
                        $q->where('action', '!=', 'Added');
                     })->get();

      foreach ($medicalArticles as $medicalArticle) {
         $medicalArticle->searchable();
         // $providerAndItsTypes = ProviderAndItsType::where('provider', $provider->provider_sync_reference->table_id)->get()->searchable();
         $medicalArticle->geolocalization_sync_reference->action = 'Added';
         $medicalArticle->geolocalization_sync_reference->update();
      }
      // Artisan::call("scout:flush", ["model" => "\App\\Models\\ArticleContentMaker\\Geolocalization"]);
      // $called = Artisan::call("scout:import", ["model" => "\App\\Models\\ArticleContentMaker\\Geolocalization"]);
      // if($called == 0) SyncReference::where('sync_class', 'Geolocalization')->delete();
      $unsyncedGeolocalizations = Geolocalization::with('article_item')->get();
      $syncedGeolocalizationsCount = 0;
      $unsyncedGeolocalizationsCount = 0;
      foreach ($unsyncedGeolocalizations as $key => $unsyncedGeolocalization) {
         if($unsyncedGeolocalization->article_item == null || $unsyncedGeolocalization->article_item->geolocalization_template == null) {
            $unsyncedGeolocalizationsCount++;
         } else {
            $syncedGeolocalizationsCount++;
         }
      }

      $summary = [
         'synced' => $syncedGeolocalizationsCount,
         'unsynced' => $unsyncedGeolocalizationsCount,
      ];

      return response()->json($summary);
   }

   public function destroy(Request $request)
   {
      $geolocalization = Geolocalization::find($request->id);

      if($geolocalization->article_item->geolocalization_sync_reference != null) {

         $geolocalization->article_item->geolocalization_sync_reference()->updateOrCreate([
            'table_id'   => $geolocalization->article_item->id,
         ],[
            'sync_class' => 'Geolocalization',
            'action' => $geolocalization->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'geolocalizations',
         ]);

      }
      // $geolocalization->geolocalization_sync_reference()->delete();

      $geolocalization->delete();

      return response()->json(true);
   }

   public function destroyImage(Request $request)
   {
      $this->removeImage($request->id);

      $geolocalization = GeolocalizeImage::find($request->id);

      if($geolocalization->geolocalization->article_item->geolocalization_sync_reference != null) {
         $geolocalization->geolocalization->article_item->geolocalization_sync_reference()->updateOrCreate([
            'table_id'   => $geolocalization->geolocalization->article_item->id,
         ],[
            'sync_class' => 'Geolocalization',
            'action' => $geolocalization->geolocalization->article_item->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'geolocalizations',
         ]);
      };

      $geolocalization->delete();
      
      return response()->json(true);
   }

   public function getServiceTypeName( Request $request ) {
      // dd($request->id());
      $serviceType = ServiceType::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $serviceType->name) );
      // dd($message);
      return response() -> json($message);

  }

   public function uploadImages($geolocalization, $image)
   {           
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $geolocalization->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'geolocalize-images',
         'file' => $image,
      ]);

      $image->deleteDirectory();

      if ($image && $image->upload()) {;

         $geolocalization->image = $image->imageUrl();
         $geolocalization->save();
      }
   }

   public function removeImage($id)
   {
      
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $id,
         's3_storage_path' => 'customer-care-tool',
         's3_folder_path' => config('access.s3_path.medical_term'),
         'file' => '',
      ]);
      $image->deleteDirectory();
   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }
}
