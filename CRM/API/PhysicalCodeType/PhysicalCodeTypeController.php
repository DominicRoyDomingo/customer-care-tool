<?php

namespace CRM\API\PhysicalCodeType;

use App\Http\Controllers\Controller;
use App\Models\PhysicalCodeType;
use App\Http\Requests\Backend\PhysicalCodeType\StorePhysicalCodeTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PhysicalCodeTypeController extends Controller
{

   public function index(Request $request)
   {
      // $lang = $request->lang;

      // $physicalCodeTypes = PhysicalCodeType::all();

      // return response()->json($physicalCodeTypes);

      $lang = $request->lang;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      if($request->sortbyLang) {
         if($request->sortbyLang !== 'all') $request['locale'] = $request->sortbyLang;
      }

      $physicalCodeTypes = PhysicalCodeType::select('id','name','created_at', 'updated_at');

      if($search != null) {
         $physicalCodeTypes = $physicalCodeTypes->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $physicalCodeTypes = $physicalCodeTypes->get();
      $physicalCodeTypes = $physicalCodeTypes->map(function ($physicalCodeType) use($lang){
         // dd($service->service_name);
         return [
               'id'              => $physicalCodeType->id, 
               'name'            => $physicalCodeType->physical_code_type_name, 
               'created_at'      => $physicalCodeType->created_at->toDateTimeString(), 
               'updated_at'      => $physicalCodeType->updated_at->toDateTimeString(),
               'has_translation' => $physicalCodeType->has_translation
         ];
      });
   //   dd($physicalCodeTypes);
      switch ($request->sort_name) {
         case 'name':
            $physicalCodeTypes = $physicalCodeTypes->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $physicalCodeTypes = $physicalCodeTypes->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $physicalCodeTypes = $physicalCodeTypes->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $physicalCodeTypes = $this->paginate($physicalCodeTypes, $entries, $page);
         return response()->json($physicalCodeTypes);
      }

      $physicalCodeTypes = $physicalCodeTypes
         ->filter(function ($physicalCodeType) {
            if (!$physicalCodeType['has_translation']) return $physicalCodeType;
         })
         ->values();
      
      $physicalCodeTypes = $this->paginate($physicalCodeTypes, $entries, $page);
      // dd($physicalCodeTypes);
      return response()->json($physicalCodeTypes);
   }

   public function all(Request $request)
   {
      $lang = $request->lang;

      $physicalCodeTypes = PhysicalCodeType::all();

      return response()->json($physicalCodeTypes);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);
      
      return response()->json($brand);
   }

   // for Category here
   public function store(StorePhysicalCodeTypeRequest $request)
   {
      $lang = $request->lang;
      $name = ucwords($request->physical_code_type_name);

      if (is_null($request->id)) {

         $physicalCodeType = new PhysicalCodeType;
         $physicalCodeType->name             = json_encode([
                                       $lang => $name,
                                    ]);
         $physicalCodeType->save();
         $physicalCodeType->get();

      } else {

         $physicalCodeType = PhysicalCodeType::findOrFail($request->id);
         $physicalCodeTypeVal = string_add_json( $lang, ucwords( $request->physical_code_type_name ), string_remove( $lang, $physicalCodeType->name));
         $physicalCodeType->name = $physicalCodeTypeVal;
         $physicalCodeType->update();

      }

      $physicalCodeType->name = getTranslation($physicalCodeType->name, $lang);

      return response()->json($physicalCodeType);
   }

   public function destroy(Request $request)
   {
      $physicalCodeType = PhysicalCodeType::find($request->id);
     
      if ( $physicalCodeType -> delete() ) {

         return response()->json(true);

      }
      
      return response()->json(false);
   }

   public function getPhysicalCodeTypeName( Request $request ) {
      // dd($request->id());
      $physicalCodeType = PhysicalCodeType::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $physicalCodeType->name) );
      // dd($message);
      return response() -> json($message);

   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }
}
