<?php

namespace CRM\API\Service;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Requests\Backend\Service\StoreServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use App\Models\MedicalStuff\MedicalTermType;
use App\Models\MedicalStuff\MedicalTerm;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\General\ImageHelper;

class ServiceController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;    
      $serviceSearch = $request->service_search;
      $brand_id = $request->brand_id;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      if($request->sortbyLang) {
         if($request->sortbyLang !== 'all') $request['locale'] = $request->sortbyLang;
      }
      $services = Service::select('id','name','service_type', 'created_at', 'updated_at', 'description')->with([
        'service_type_item',
        'services_brand_item',
      ]);
      if($request->group == true) {
         if($request->service_type == true) {
            $services->Where(function($subQuery) use ($serviceSearch){  
               $subQuery->whereHas('service_type_item', function ( $query ) use ($serviceSearch) {
                   $query->where('name',  'LIKE', '%' .$serviceSearch . '%' );
               });
           });
         }
         
         $services = $services->where(function($subQuery) use ($brand_id){   
            $subQuery->whereHas('services_brand_item', function ( $query ) use ($brand_id) {
               $query->where('brands',  $brand_id);
            });
         });
         $services = $services->get();

         $services = $services->groupBy(function ($serv) use($lang){
            return getTranslation($serv->service_type_item->name, $lang);
         });
         
         $services = $services->map(function ($service) use($lang){
            return $service->map(function ($servic) use($lang){
               return [
                  'id' => $servic->id, 
                  'name' => getTranslation($servic->name,$lang), 
                  'service_type' => $servic->service_type, 
                  'created_at' => $servic->created_at->toDateTimeString(), 
                  'updated_at' => $servic->updated_at->toDateTimeString(), 
                  'service_type_item'  => [
                     'id' => $servic->service_type_item->id,
                     'name' => getTranslation($servic->service_type_item->name,$lang),
                  ],
               ];
            });
         });

         return response()->json($services);

      }
      
      if($search != null) {
         $services = $services->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%')
         ->orWhere('description', 'LIKE', '%' . $search . '%')
         ->orWhere(function($subQuery) use ($search){   
            
            $subQuery->whereHas('service_type_item', function ( $query ) use ($search) {
                $query->where('name',  'LIKE', '%' .$search . '%' );
            });
        });
      }

      if($request->service_name == true) {
         $services = $services->where('name', 'LIKE', '%' . $serviceSearch . '%');
      }

      if($request->serviceTypeId != null) {
         $services = $services->where('service_type', $request->serviceTypeId);

         $services = $services->get();
         $services = $services->map(function ($service) use($lang){
            return [
                  'id' => $service->id, 
                  'name' => getTranslation($service->name,$lang), 
                  'service_type' => $service->service_type, 
                  'created_at' => $service->created_at->toDateTimeString(), 
                  'updated_at' => $service->updated_at->toDateTimeString(), 
                  'service_type_item'  => [
                     'id' => $service->service_type_item->id,
                     'name' => getTranslation($service->service_type_item->name,$lang),
                  ],
            ];
         });

         return response()->json($services);
      }
     
      $services = $services->where(function($subQuery) use ($brand_id){   
         $subQuery->whereHas('services_brand_item', function ( $query ) use ($brand_id) {
            $query->where('brands',  $brand_id);
         });
      });
      $services = $services->get();

      if ($request->sortbyLang && $request->sortbyLang != 'all') {
         $services = $services->map(function ($service) use($lang){
            return [
                  'id'           => $service->id, 
                  'name'         => getTranslation($service->name,$lang), 
                  'service_type' => $service->service_type, 
                  'brands'       => $service->selected_brands,
                  'service_name' => getTranslation($service->name,$lang),
                  'created_at'   => $service->created_at->toDateTimeString(), 
                  'updated_at'   => $service->updated_at->toDateTimeString(),
                  'service_type_item'  => [
                     'id' => $service->service_type_item->id,
                     'name' => getNewTranslation($service->service_type_item->name,$lang),
                  ],
                  'has_translation' => $service->has_translation
            ];
         });

         $services = $this->sortServices($request->sort_name, $services, $sortDesc);

         $services = $services
            ->filter(function ($service) {
               if (!$service['has_translation']) return $service;
            })
            ->values();
         $services = $this->paginate($services, $entries, $page);

         return response()->json($services);
      }

      if($entries != null && $page != null) {
      
         $services = $services->map(function ($service) use($lang){
            return [
                  'id'           => $service->id, 
                  'name'         => getTranslation($service->name,$lang), 
                  'service_type' => $service->service_type, 
                  'brands'       => $service->selected_brands,
                  'service_name' => $service->service_name,
                  'created_at'   => $service->created_at->toDateTimeString(), 
                  'updated_at'   => $service->updated_at->toDateTimeString(),
                  'service_type_item'  => [
                     'id' => $service->service_type_item->id,
                     'name' => $service->service_type_item->service_type_name,
                  ],
                  'has_translation' => $service->has_translation
            ];
         });

         $services = $this->sortServices($request->sort_name, $services, $sortDesc);

         $services = $this->paginate($services, $entries, $page);
      }
      
      
      // dd($services);
      if($entries != null && $page != null) {
         
      } 

      return response()->json($services);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);
      
      return response()->json($brand);
   }

   // for Category here
   public function store(StoreServiceRequest $request)
   {
      $lang = $request->lang;
      $serviceName = ucwords($request->name);
      // dd($request->locale);
      if (is_null($request->id)) {

         $service = new Service;

         $service->name             = json_encode([
                                       $lang => $serviceName,
                                    ]);
         $service->service_type     = $request->service_type;
         $service->description      = $request->description;
         $service->created_by       = auth()->user()->id;
         $service->last_modified_by = auth()->user()->id;
         $service->save();
            
         if($request->images != null) $this->uploadImages($service, $request->file('images'));

         $service->services_brand_item()->create([
            "brands"  => $request->brand_id,
         ]);

         $service->with(['service_type_item'])->get();

         if($request->isMedical == true) {
            $medicalTermType = MedicalTermType::where('name', 'like', '%Service%')->first();

            $medicalTerm = MedicalTerm::create([
               'name' => json_encode([
                  $lang => $serviceName,
               ]),
               'term_types' => json_encode(["term_types" => [$medicalTermType->id]]),
            ]);

            $service = Service::findOrFail($service->id);
            $service->medical_term  = $medicalTerm->id;
            $service->update();
         }

      } else {

         $service = Service::findOrFail($request->id);
         $serviceVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $service -> name ) );
         $service->name             = $serviceVal;
         $service->service_type     = $request->service_type;
         $service->description      = $request->description;
         $service->last_modified_by = auth()->user()->id;
         $service->update();

         if($request->images != null) $this->uploadImages($service, $request->file('images'));

         $service = Service::with(['service_type_item'])->findOrFail($request->id);
      }
      
      $service->name = getTranslation($service->name, $lang);
      $service->service_type_item->name = $service->service_type_item->service_type_name;
      return response()->json($service);
   }

   public function destroy(Request $request)
   {
      $service = Service::find($request->id);
      // dd();
      $medicalTerm = MedicalTerm::find($service->medical_term);

      if(!$service->provider_services()->exists()) {
         if ( $service -> delete() ) {
            
            if($service->medical_term_item()->exists()) {
               $service->medical_term_item()->delete();
            }
            return response()->json(true);
   
         }
      }
      return response()->json(false);
   }

   public function getServiceName( Request $request ) 
   {
         // dd($request->id());
         $service = Service::whereId( $request->id ) -> first();
         // dd($organizationCategory);
         $message = ucwords( string_to_value( $request->lang , $service->name) );
         // dd($message);
         return response() -> json($message);

   }

   public function checkMedicalType( Request $request ) 
   {
         $medicals = MedicalTermType::where('name', 'like', '%Service%')->count();

         if($medicals == 0) {
            return response()->json(false);
         }

         return response()->json(true);
   }

   public function checkIfHasLinkOrArticle(Request $request) 
   {
      $service = Service::with('medical_term_item.medical_articles')->findOrFail($request->id);
      // dd($service);
      if($service->medical_term_item != null) {
         if(count($service->medical_term_item->medical_articles)) {
            return response()->json(true);
         }
      }
      return response()->json(false);
   }

   public function linkBrand(Request $request)
   {
      $request->validate([
            'brands'         => 'required',
         ],
         [
            'brands.required'    => 'Brand Name is Required',
         ]
      );
      
      $lang = $request->lang;
      $service = Service::findOrFail($request->id);
      $service->services_brand_item()->delete();

      foreach(json_decode($request->brands) as $value){
         $service->services_brand_item()->create([
            "brands"  => $value,
         ]);
      };

      $service = service::with(['services_brand_item',])->findOrFail($request->id);
      $service->name = getTranslation($service->name, $lang);
      $service->service_type_item->name = getTranslation($service->service_type_item->name, $lang);
      return response()->json($service);
   }

   public function sortServices($sortBy, $services, $sortDesc) {
      switch ($sortBy) {
         case 'name':
            return $services->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'service_type':
            return $services->sortBy(function ($service, $key) {
               return $service['service_type_item']['name'];
            })->values();
            break;
         case 'updated_at':
            return $services->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            return $services->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }
   }


   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }

   public function uploadImages($service, $images)
   {           
      $betaImage = new ImageHelper([
         'driver' => 's3',
         'id' => $service->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'service-images/',
         'file' => $images[0],
      ]);

      $betaImage->deleteDirectory();

      $imagesUrlArr = array();

      foreach($images as $image) {
         $finalImage = new ImageHelper([
            'driver' => 's3',
            'id' => $service->id,
            's3_storage_path' => 'med4care-customercare',
            's3_folder_path' => 'service-images/',
            'file' => $image,
         ]);
         array_push( $imagesUrlArr, $finalImage->imageUrl() );
         $finalImage->upload();
      }

      $service->images = $imagesUrlArr;
      $service->save();
   }
}
