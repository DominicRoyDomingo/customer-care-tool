<?php

namespace CRM\API\ServiceType;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use App\Http\Requests\Backend\ServiceType\StoreServiceTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Helpers\General\ImageHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceTypeController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      if($request->sortbyLang) {
         if($request->sortbyLang !== 'all') $request['locale'] = $request->sortbyLang;
      }

      $serviceTypes = ServiceType::select('id','name','created_at', 'updated_at', 'images');

      if($search != null) {
         $serviceTypes = $serviceTypes->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $serviceTypes = $serviceTypes->get();
      $serviceTypes = $serviceTypes->map(function ($serviceType) use($lang){
         // dd($service->service_name);
         return [
               'id'              => $serviceType->id, 
               'name'            => $serviceType->service_type_name, 
               'created_at'      => $serviceType->created_at->toDateTimeString(), 
               'updated_at'      => $serviceType->updated_at->toDateTimeString(),
               'has_translation' => $serviceType->has_translation
         ];
      });
   //   dd($serviceTypes);
      switch ($request->sort_name) {
         case 'name':
            $serviceTypes = $serviceTypes->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $serviceTypes = $serviceTypes->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $serviceTypes = $serviceTypes->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $serviceTypes = $this->paginate($serviceTypes, $entries, $page);
         return response()->json($serviceTypes);
      }

      $serviceTypes = $serviceTypes
         ->filter(function ($serviceType) {
            if (!$serviceType['has_translation']) return $serviceType;
         })
         ->values();
      
      $serviceTypes = $this->paginate($serviceTypes, $entries, $page);
      // dd($serviceTypes);
      return response()->json($serviceTypes);
   }

   public function all(Request $request)
   {
      $lang = $request->lang;
      // dd('test');

      $serviceTypes = ServiceType::all()->map(function ($serviceType) use($lang){

         $serviceType['name'] = getTranslation($serviceType['name'], $lang);
     
         return $serviceType;
     
      });

      return response()->json($serviceTypes);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);
      
      return response()->json($brand);
   }

   // for Category here
   public function store(StoreServiceTypeRequest $request)
   {
      $lang = $request->lang;
      // dd($lang);
      $serviceTypeName = ucwords($request->service_type_name);

      if (is_null($request->id)) {
         $serviceType = new ServiceType;

         $serviceType->name = json_encode([
                                 $lang => $serviceTypeName,
                              ]);
         $serviceType->save();

         if($request->images != null) $this->uploadImages($serviceType, $request->file('images'));

         $serviceType->name = getTranslation($serviceType->name, $lang);

      } else {

         $serviceType = ServiceType::findOrFail($request->id);

         $serviceTypeVal = string_add_json( $lang, ucwords( $request -> service_type_name ), string_remove( $lang, $serviceType -> name ) );

         $serviceType->name = $serviceTypeVal;

         $serviceType->update();

         if($request->images != null) $this->uploadImages($serviceType, $request->file('images'));

         $serviceType->name = getTranslation($serviceType->name, $lang);

      }

      return response()->json($serviceType);
   }

   public function destroy(Request $request)
   {
      ServiceType::where('id',$request->id)->delete();

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
