<?php

namespace CRM\API\GeolocalizationTemplate;

use App\Http\Controllers\Controller;
use App\Models\ArticleContentMaker\GeolocalizationTemplate;
use App\Models\ArticleContentMaker\Geolocalization;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\MedicalStuff\PublishingItemContent;
use App\Http\Requests\Backend\GeolocalizationTemplate\StoreGeolocalizationTemplateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Helpers\General\ImageHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GeolocalizationTemplateController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $articleId = $request->article_id;

      $article = MedicalArticle::with('geolocalizations')->findOrFail($articleId);
   
      // $geolocalizations = ;

      if($search != null) {
         $geolocalizations = $geolocalizations->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $geolocalizations = $article->geolocalizations;
      $geolocalizations = $geolocalizations->map(function ($geolocalization) use($lang){
         $division = $geolocalization->division_item != null ? $geolocalization->division_item->name.', ' : "";
         return [
               'id'           => $geolocalization->id, 
               'place'        => $geolocalization->city_item->name.', '. $division .$geolocalization->country_item->name,
               'country_id'   => $geolocalization->country,
               'division_id'  => $geolocalization->division,
               'city_id'      => $geolocalization->city,
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

   // public function show(Request $request, $id)
   // {
   //    $brand = $this->brandRepository->getById($id);
      
   //    return response()->json($brand);
   // }

   public function getTemplate(Request $request)
   {
      $articleId = $request->article_id;
      $geolocalizationTemplate = PublishingItemContent::where('publishing_item', $articleId)->first();
      
      return response()->json($geolocalizationTemplate);
   }

   // for Category here
   public function store(StoreGeolocalizationTemplateRequest $request)
   {
      $lang = $request->lang;
      $articleId = $request->article_id;
      
      if (is_null($request->id)) {
    
         $geolocalizationTemplate = new PublishingItemContent;

         $geolocalizationTemplate->publishing_item    = $articleId;
         // $geolocalizationTemplate->page_title         = $request->title;
         $geolocalizationTemplate->content            = $request->body;
         $geolocalizationTemplate->meta_description   = $request->meta_description;
         $geolocalizationTemplate->slug               = $request->slug;
         $geolocalizationTemplate->alt_tag_image      = $request->alt_tag_image;
         $geolocalizationTemplate->img_description    = $request->image_description;
         $geolocalizationTemplate->save();
         $geolocalizationTemplate->get();

         // $medicalArticle = MedicalArticle::where('id',$articleId)->first();

         // if($medicalArticle->geolocalization_sync_reference != null) {
         //    $medicalArticle->geolocalization_sync_reference()->updateOrCreate([
         //       'table_id'   => $medicalArticle->id,
         //    ],[
         //       'sync_class' => 'Geolocalization',
         //       'action' => isset($medicalArticle->geolocalization_sync_reference->action) ? $medicalArticle->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
         //       'source_table' => 'geolocalizations',
         //    ]);
         // };

      } else {

         $place = json_decode($request->place);
         $geolocalizationTemplate = PublishingItemContent::findOrFail($request->id);
         $geolocalizationTemplate->publishing_item    = $articleId;
         // $geolocalizationTemplate->page_title         = $request->title;
         $geolocalizationTemplate->content            = $request->body;
         $geolocalizationTemplate->meta_description   = $request->meta_description;
         $geolocalizationTemplate->slug               = $request->slug;
         $geolocalizationTemplate->alt_tag_image      = $request->alt_tag_image;
         $geolocalizationTemplate->img_description    = $request->image_description;

         $geolocalizationTemplate->update();

         // $medicalArticle = MedicalArticle::where('id',$articleId)->first();

         // if($medicalArticle->geolocalization_sync_reference != null) {
         //    $medicalArticle->geolocalization_sync_reference()->updateOrCreate([
         //       'table_id'   => $medicalArticle->id,
         //    ],[
         //       'sync_class' => 'Geolocalization',
         //       'action' => isset($medicalArticle->geolocalization_sync_reference->action) ? $medicalArticle->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
         //       'source_table' => 'geolocalizations',
         //    ]);
         // };

      }

      $article = MedicalArticle::withoutSyncingToSearch(function () use ($request, $articleId, $lang) {
         $article = MedicalArticle::with('geolocalization_sync_reference')->findOrFail($articleId);
         $article->title = to_json_add($lang, $request->title);
         $article->update();
         
         return $article;
      });
      
      if($article->geolocalization_sync_reference != null) {
         $article->geolocalization_sync_reference()->updateOrCreate([
            'table_id'   => $article->id,
         ],[
            'sync_class' => 'Geolocalization',
            'action' => isset($article->geolocalization_sync_reference->action) ? $article->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
            'source_table' => 'geolocalizations',
         ]);
      };

      return response()->json($geolocalizationTemplate);
   }

   public function destroy(Request $request)
   {
      Geolocalization::where('id',$request->id)->delete();
      
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

  public function uploadImages($serviceType, $images)
   {           
      $betaImage = new ImageHelper([
         'driver' => 's3',
         'id' => $serviceType->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'service-type-images/',
         'file' => $images[0],
      ]);

      $betaImage->deleteDirectory();

      $imagesUrlArr = array();

      foreach($images as $image) {
         $finalImage = new ImageHelper([
            'driver' => 's3',
            'id' => $serviceType->id,
            's3_storage_path' => 'med4care-customercare',
            's3_folder_path' => 'service-type-images/',
            'file' => $image,
         ]);
         array_push( $imagesUrlArr, $finalImage->imageUrl() );
         $finalImage->upload();
      }

      $serviceType->images = $imagesUrlArr;
      $serviceType->save();
   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }
}
