<?php

namespace CRM\API\ProviderService;

use App\Http\Controllers\Controller;
use App\Models\ProviderService;
use App\Models\SyncReference;
use App\Models\Provider;
use App\Models\MedicalStuff\MedicalTerm;
use App\Http\Requests\Backend\ProviderService\StoreProviderServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ProviderServiceController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $id = $request->id;
      $entries = $request->entries;
      $page = $request->page;

      $providerServices = ProviderService::select('id','providers','services', 'day_start', 'day_end', 'description', 'parameter')->where('providers', $id)->with([
         'service_item',
         'provider_item',
         'parameter_item',
         ])->paginate($entries, ['*'], 'page', $page);
      
      $providerServices = $providerServices->toArray();
      // dd($providerServices);
      $providerServices['data'] = collect($providerServices['data'])->map(function ($providerService) use($lang){
          return [
               'id' => $providerService['id'], 
               'services' => $providerService['services'],
               'providers' => $providerService['providers'], 
               'day_start' => $providerService['day_start'], 
               'day_end' => $providerService['day_end'], 
               'description' => $providerService['description'], 
               'service_item'  => [
                  'id' => $providerService['service_item']['id'],
                  'name' => getTranslation($providerService['service_item']['name'],$lang),
                  'base_name' => getTranslation($providerService['service_item']['name'],$lang),
               ],
               'provider_item'  => [
                  'id' => $providerService['provider_item']['id'],
                  'name' => getTranslation($providerService['provider_item']['name'],$lang),
                  'provider_types' => $providerService['provider_and_its_type'],
               ],
               'parameter_item'  => $providerService['parameter_item'] == null ? null :[
                  'id' => $providerService['parameter_item']['id'],
                  'name' => $providerService['parameter_item']['name'],
               ],
          ];
       });

      return response()->json($providerServices);
   }

   // for Category here
   public function store(StoreProviderServiceRequest $request)
   {
      $lang = $request->lang;
      $articleKeywords = [
         'articolo geolocalizzato', 
         'articoli geolocalizzati', 
         'geolocalized articles'
      ];
      if (is_null($request->id)) {
         $providerServicesArr = array();
         foreach(json_decode($request->services, true) as $service) {
            array_push($providerServicesArr, $service);
         }
         $providerServicesToAdd = array_diff($providerServicesArr, ProviderService::where('providers', $request->providers)->pluck('services')->toArray());
         $providerServicesToDelete = array_diff(ProviderService::where('providers', $request->providers)->pluck('services')->toArray(), $providerServicesArr);
         foreach ($providerServicesToDelete as $service) {
            $medicalArticles = MedicalTerm::where('id', $service)->first()->medical_articles;
            if(!$medicalArticles->isEmpty()) {
               foreach ($medicalArticles as $medicalArticle) {
                  if(!$medicalArticle->publishing_item_type_articles->isEmpty()) {
                     foreach ($medicalArticle->publishing_item_type_articles as $article) {
                        if(in_array(strtolower($article->base_name), $articleKeywords, true) ) {
                           if($medicalArticle->geolocalization_sync_reference != null) {
                              $medicalArticle->geolocalization_sync_reference()->updateOrCreate([
                                 'table_id'   => $medicalArticle->id,
                              ],[
                                 'sync_class' => 'Geolocalization',
                                 'action' => isset($medicalArticle->geolocalization_sync_reference->action) ? $medicalArticle->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
                                 'source_table' => 'geolocalizations',
                              ]);
                           };
                        }
                     }
                  };
               }
            };
            ProviderService::where('providers', $request->providers)->where('services', $service)->first()->delete();
         }
         
         foreach($providerServicesToAdd as $value){
            $medicalArticles = MedicalTerm::where('id', $value)->first()->medical_articles;
            if(!$medicalArticles->isEmpty()) {
               foreach ($medicalArticles as $medicalArticle) {
                  if(!$medicalArticle->publishing_item_type_articles->isEmpty()) {
                     foreach ($medicalArticle->publishing_item_type_articles as $article) {
                        if(in_array(strtolower($article->base_name), $articleKeywords, true) ) {
                           if($medicalArticle->geolocalization_sync_reference != null) {
                              $medicalArticle->geolocalization_sync_reference()->updateOrCreate([
                                 'table_id'   => $medicalArticle->id,
                              ],[
                                 'sync_class' => 'Geolocalization',
                                 'action' => isset($medicalArticle->geolocalization_sync_reference->action) ? $medicalArticle->geolocalization_sync_reference->action != 'New' ? 'Update' : 'New' : 'New',
                                 'source_table' => 'geolocalizations',
                              ]);
                           };
                        }
                     }
                  };
               }
            };

            $providerService = new ProviderService;
            $providerService->providers         = $request->providers;
            $providerService->services          = $value;
            $providerService->day_start         = Carbon::now()->toDateString();
            $providerService->created_by        = auth()->user()->id;
            $providerService->last_modified_by  = auth()->user()->id;
            $providerService->save();
         };
         $providerService = ProviderService::where('providers', $request->providers)->with(['service_item','provider_item','parameter_item'])->get();

         $providerService = $providerService->map(function ($providerService) use($lang){
            return [
                  'id' => $providerService->id, 
                  'services' => $providerService->services,
                  'providers' => $providerService->providers, 
                  'day_start' => $providerService->day_start, 
                  'day_end' => $providerService->day_end, 
                  'description' => $providerService->description, 
                  'service_item'  => [
                     'id' => $providerService->service_item->id,
                     'name' => getTranslation($providerService->service_item->name,$lang),
                     'base_name' => getTranslation($providerService['service_item']['name'],$lang),
                  ],
                  'provider_item'  => [
                    'id' => $providerService->provider_item->id,
                    'name' => getTranslation($providerService->provider_item->name,$lang),
                    'provider_type' => $providerService['provider_item']['provider_type'],
                 ],
                 'parameter_item'  => $providerService['parameter_item'] == null ? null :[
                  'id' => $providerService['parameter_item']['id'],
                  'name' => $providerService['parameter_item']['name'],
               ],
            ];
         });

      } else {
         $providerService = ProviderService::findOrFail($request->id);
         $providerService->providers         = $request->providers;
         $providerService->services          = $request->services;
         $providerService->day_end           = $request->day_end == 'null' ? '0000-00-00' : $request->day_end;
         $providerService->day_start         = $request->day_start;
         $providerService->description       = $request->description;
         $providerService->parameter         = $request->parameter;
         $providerService->last_modified_by  = auth()->user()->id;
         $providerService->save();
         $providerService = [
               'id' => $providerService->id, 
               'services' => $providerService->services,
               'providers' => $providerService->providers, 
               'day_start' => $providerService->day_start, 
               'day_end' => $providerService->day_end, 
               'description' => $providerService->description, 
               'service_item'  => [
                  'id' => $providerService->service_item->id,
                  'name' => getTranslation($providerService->service_item->name,$lang),
                  'base_name' => getTranslation($providerService->service_item->name,$lang),
               ],
               'provider_item'  => [
                  'id' => $providerService->provider_item->id,
                  'name' => getTranslation($providerService->provider_item->name,$lang),
                  'provider_type' => $providerService['provider_item']['provider_type'],
               ],
               'parameter_item'  => $providerService['parameter_item'] == null ? null :[
               'id' => $providerService['parameter_item']['id'],
               'name' => $providerService['parameter_item']['name'],
            ],
         ];
      }
      
      $provider = Provider::with(['provider_sync_reference',])->findOrFail($request->providers);
      if($provider->provider_sync_reference != null) {
         $provider->provider_sync_reference()->updateOrCreate([
            'table_id'   => $provider->id,
         ],[
            'sync_class' => 'Provider',
            'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'providers',
         ]);
      }

      return response()->json($providerService);
   }

   public function getProviderServiceItems(Request $request)
   {
      $lang = $request->lang;
      $id = $request->id;

      $providerServices = ProviderService::select('id','providers','services', 'day_start', 'day_end', 'description', 'parameter')->where('providers', $id)->with([
         'service_item',
         ])->get()->pluck('service_item.id')->toArray();

      return response()->json($providerServices);
   }

   public function getAll(Request $request) {
      $lang = $request->lang;
      $id = $request->id;

      $providerServices = ProviderService::select('id','providers','services', 'day_start', 'day_end', 'description', 'parameter')->where('providers', $id)->with([
         'service_item',
         ])->get();
      // dd($providerServices);
      $providerServices = $providerServices->map(function ($providerService) use($lang){
            return [
               'id' => $providerService['id'], 
               'services' => $providerService['services'],
               'providers' => $providerService['providers'], 
               'day_start' => $providerService['day_start'], 
               'day_end' => $providerService['day_end'], 
               'description' => $providerService['description'], 
               'service_item'  => [
                  'id' => $providerService['service_item']['id'],
                  'name' => getTranslation($providerService['service_item']['name'],$lang),
                  'images' => $providerService['service_item']['images'],
               ],
            ];
         });

      return response()->json($providerServices);
   }

   public function destroy(Request $request)
   {
      $providerService = ProviderService::find($request->id);
      $providerService->delete();

      return response()->json(true);
   }

   public function update_provider_sync_reference($term) {

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
            if($provider->provider_sync_reference != null) {
               $provider->provider_sync_reference()->updateOrCreate([
                  'table_id'   => $provider->id,
               ],[
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
            $service->provider_item->provider_sync_reference()->updateOrCreate([
               'table_id'   => $service->provider_item->id,
            ],[
               'sync_class' => 'Provider',
               'action' => $service->provider_item->provider_sync_reference == null ? 'New' : $service->provider_item->provider_sync_reference->action != 'New' ? 'Update' : 'New',
               'source_table' => 'providers',
            ]);
         }
      }
      
   }

   public function get_available_language($name, $lang)
   {
      $availableLanguage = array_key_first(json_decode($name, true));

      return isset(json_decode($name)->$lang) ? $lang : $availableLanguage;
   }

}
