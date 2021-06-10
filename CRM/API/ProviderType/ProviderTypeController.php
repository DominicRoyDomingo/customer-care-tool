<?php

namespace CRM\API\ProviderType;

use App\Http\Controllers\Controller;
use App\Models\ProviderType;
use App\Http\Requests\Backend\ProviderType\StoreProviderTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderTypeController extends Controller
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

      $providerTypes = ProviderType::select('id','name','created_at', 'updated_at');

      if($search != null) {
         $providerTypes = $providerTypes->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $providerTypes = $providerTypes->get();
      $providerTypes = $providerTypes->map(function ($providerType) use($lang){
         // dd($provider->provider_name);
         return [
               'id'              => $providerType->id, 
               'name'            => $providerType->provider_type_name, 
               'created_at'      => $providerType->created_at->toDateTimeString(), 
               'updated_at'      => $providerType->updated_at->toDateTimeString(),
               'has_translation' => $providerType->has_translation
         ];
      });
   //   dd($serviceTypes);
      switch ($request->sort_name) {
         case 'name':
            $providerTypes = $providerTypes->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $providerTypes = $providerTypes->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $providerTypes = $providerTypes->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $providerTypes = $this->paginate($providerTypes, $entries, $page);
         return response()->json($providerTypes);
      }

      $providerTypes = $providerTypes
         ->filter(function ($providerType) {
            if (!$providerType['has_translation']) return $providerType;
         })
         ->values();
      // dd($providerTypes);
      $providerTypes = $this->paginate($providerTypes, $entries, $page);
      
      return response()->json($providerTypes);
   }

   public function all(Request $request)
   {
      $lang = $request->lang;

      $providerTypes = ProviderType::all()->map(function ($providerType) use($lang){

         $providerType['name'] = getTranslation($providerType['name'], $lang);
     
         return $providerType;
     
      });

      return response()->json($providerTypes);
   }

   // for Category here
   public function store(StoreProviderTypeRequest $request)
   {
      $lang = $request->lang;
      // dd($lang);
      $providerTypeName = ucwords($request->provider_type_name);

      if (is_null($request->id)) {

         $providerType = ProviderType::create([
            "name"      => json_encode([
                        $lang => $providerTypeName,
                        ]),
         ]);

      } else {

         $providerType = ProviderType::findOrFail($request->id);

         $providerTypeVal = string_add_json( $lang, ucwords( $request -> provider_type_name ), string_remove( $lang, $providerType -> name ) );
         // dd($providerTypeVal);
         $providerType->update([
            "name"      => $providerTypeVal,
         ]);

      }
      // dd($providerType);
      $providerType->name = getTranslation($providerType->name, $lang);

      return response()->json($providerType);
   }

   public function destroy(Request $request)
   {
      ProviderType::where('id',$request->id)->delete();

      return response()->json(true);
   }

   public function getProviderTypeName( Request $request ) {
      // dd($request->id());
      $providerType = ProviderType::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $providerType->name) );
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
