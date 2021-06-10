<?php

namespace CRM\API\ServicesExclusive;

use App\Http\Controllers\Controller;
use App\Models\ServicesExclusive;
use App\Http\Requests\Backend\ServicesExclusive\StoreServicesExclusiveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\General\ImageHelper;

class ServicesExclusiveController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;    
      $servicesExclusiveSearch = $request->services_eclusive_search;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $servicesExclusives = ServicesExclusive::select('id','text_display','service_type', 'created_at', 'updated_at')->with([
         'service_type_item',
         ]);
      
      if($search != null) {
         $servicesExclusives = $servicesExclusives->where('text_display', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%')
         ->orWhere('description', 'LIKE', '%' . $search . '%')
         ->orWhere(function($subQuery) use ($search){   
            
            $subQuery->whereHas('service_type_item', function ( $query ) use ($search) {
                $query->where('name',  'LIKE', '%' .$search . '%' );
            });
        });
      }

      $servicesExclusives = $servicesExclusives->get();

      $servicesExclusives = $servicesExclusives->map(function ($serviceExclusive) use($lang){
         return [
               'id' => $serviceExclusive->id, 
               'text_display' => $serviceExclusive->text_display, 
               'service_type' => $serviceExclusive->service_type, 
               'created_at' => $serviceExclusive->created_at->toDateTimeString(), 
               'updated_at' => $serviceExclusive->updated_at->toDateTimeString(), 
               'service_type_item'  => [
                  'id' => $serviceExclusive->service_type_item->id,
                  'name' => getTranslation($serviceExclusive->service_type_item->name,$lang),
               ],
         ];
      });
     
      switch ($request->sort_name) {
         case 'name':
            $servicesExclusives = $servicesExclusives->sortBy('text_display', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'service_type':
            $servicesExclusives = $servicesExclusives->sortBy(function ($service, $key) {
               return $service['service_type_item']['name'];
            })->values();
            break;
         case 'updated_at':
            $servicesExclusives = $servicesExclusives->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $servicesExclusives = $servicesExclusives->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if($entries != null && $page != null) {
         $servicesExclusives = $this->paginate($servicesExclusives, $entries, $page);
      } 

      return response()->json($servicesExclusives);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);
      
      return response()->json($brand);
   }

   // for Category here
   public function store(StoreServicesExclusiveRequest $request)
   {
      $lang = $request->lang;
      $servicesExclusiveName = ucwords($request->text_display);

      if (is_null($request->id)) {

         $servicesExclusive = new ServicesExclusive;

         $servicesExclusive->text_display     = $servicesExclusiveName;
         $servicesExclusive->service_type     = $request->service;
         $servicesExclusive->save();

         $servicesExclusive->with(['service_type_item'])->get();

      } else {

         $servicesExclusive= ServicesExclusive::findOrFail($request->id);

         $servicesExclusive->text_display = $request->text_display;
         $servicesExclusive->service_type = $request->service;
         $servicesExclusive->update();

         $servicesExclusive = ServicesExclusive::with(['service_type_item'])->findOrFail($request->id);
      }

      $servicesExclusive->service_type_item->name = getTranslation($servicesExclusive->service_type_item->name, $lang);

      return response()->json($servicesExclusive);
   }

   public function destroy(Request $request)
   {
      $serviceExclusive = ServicesExclusive::find($request->id);
      
      $serviceExclusive->delete();
   
      return response()->json(true);
   }

   public function getServiceName( Request $request ) {
      // dd($request->id());
      $service = Service::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $service->name) );
      // dd($message);
      return response() -> json($message);

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
