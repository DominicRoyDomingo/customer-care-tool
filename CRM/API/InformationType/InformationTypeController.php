<?php

namespace CRM\API\InformationType;

use App\Http\Controllers\Controller;
use App\Models\InformationType;
use App\Http\Requests\Backend\InformationType\StoreInformationTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\General\ImageHelper;

class InformationTypeController extends Controller
{

   public function index(Request $request)
   {
      // $lang = $request->lang;

      // $informationTypes = InformationType::all();

      // return response()->json($informationTypes);

      $lang = $request->lang;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      if($request->sortbyLang) {
         if($request->sortbyLang !== 'all') $request['locale'] = $request->sortbyLang;
      }

      $informationTypes = InformationType::select('id','name','type_of_data','created_at', 'updated_at');

      if($search != null) {
         $informationTypes = $informationTypes->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $informationTypes = $informationTypes->get();
      $informationTypes = $informationTypes->map(function ($informationType) use($lang){
         // dd($informationType->informationType_name);
         return [
               'id'              => $informationType->id, 
               'name'            => $informationType->information_type_name, 
               'type_of_data'    => $informationType->type_of_data,
               'created_at'      => $informationType->created_at->toDateTimeString(), 
               'updated_at'      => $informationType->updated_at->toDateTimeString(),
               'has_translation' => $informationType->has_translation
         ];
      });
      
      switch ($request->sort_name) {
         case 'name':
            $informationTypes = $informationTypes->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $informationTypes = $informationTypes->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $informationTypes = $informationTypes->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $informationTypes = $this->paginate($informationTypes, $entries, $page);
         return response()->json($informationTypes);
      }

      $informationTypes = $informationTypes
         ->filter(function ($informationType) {
            if (!$informationType['has_translation']) return $informationType;
         })
         ->values();
      
      $informationTypes = $this->paginate($informationTypes, $entries, $page);
      // dd($informationTypes);
      return response()->json($informationTypes);
   }

   public function all(Request $request)
   {
      $lang = $request->lang;

      $informationTypes = InformationType::all();

      return response()->json($informationTypes);
   }

   // for Category here
   public function store(StoreInformationTypeRequest $request)
   {
      $lang = $request->lang;
      $name = ucwords($request->information_type_name);

      if (is_null($request->id)) {

         $informationType = new InformationType;
         $informationType->name           = json_encode([
                                             $lang => $name,
                                          ]);
         $informationType->type_of_data   = $request->information_type_data;
         $informationType->save();
         $informationType->get();

      } else {

         $informationType = InformationType::findOrFail($request->id);
         $informationTypeVal = string_add_json( $lang, ucwords( $request->information_type_name ), string_remove( $lang, $informationType->name));
         $informationType->name = $informationTypeVal;
         $informationType->type_of_data   = $request->information_type_data;
         $informationType->update();

      }

      $informationType->name = getTranslation($informationType->name, $lang);

      return response()->json($informationType);
   }

   public function destroy(Request $request)
   {
      $informationType = InformationType::find($request->id);
     
      if ( $informationType -> delete() ) {

         return response()->json(true);

      }
      
      return response()->json(false);
   }

   public function getInformationTypeName( Request $request ) {
      // dd($request->id());
      $informationType = InformationType::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $informationType->name) );
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
