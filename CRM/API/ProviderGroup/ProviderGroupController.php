<?php

namespace CRM\API\ProviderGroup;

use App\Http\Controllers\Controller;
use App\Models\ProviderGroup;
use App\Models\Provider;
use App\Http\Requests\Backend\ProviderGroup\StoreProviderGroupRequest;
use App\Helpers\General\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderGroupController extends Controller
{

   public function index(Request $request)
   {
      // $lang = $request->lang;

      // $providerGroups = ProviderGroup::all()->map(function ($providerGroup) use($lang){

      //    $providerGroup['name'] = getTranslation($providerGroup['name'], $lang);
     
      //    return $providerGroup;
     
      // });

      // return response()->json($providerGroups);

      $lang = $request->lang;
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      if($request->sortbyLang) {
         if($request->sortbyLang !== 'all') $request['locale'] = $request->sortbyLang;
      }

      $providerGroups = ProviderGroup::with(['providers'])->select('id','name','created_at', 'updated_at', 'image');

      if($search != null) {
         $providerGroups = $providerGroups->where('name', 'LIKE', '%' . $search . '%')
         ->orWhere('created_at', 'LIKE', '%' . $search . '%')
         ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
      }

      $providerGroups = $providerGroups->get();
      $providerGroups = $providerGroups->map(function ($providerGroup) use($lang){
         // dd($providerGroup->providerGroup_name);
         return [
               'id'              => $providerGroup->id, 
               'name'            => getTranslation($providerGroup->name, $lang), 
               'created_at'      => $providerGroup->created_at->toDateTimeString(), 
               'updated_at'      => $providerGroup->updated_at->toDateTimeString(),
               'has_translation' => $providerGroup->has_translation,
               'providers'       => $providerGroup->providers,
         ];
      });
   //   dd($providerGroups);
      switch ($request->sort_name) {
         case 'name':
            $providerGroups = $providerGroups->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'updated_at':
            $providerGroups = $providerGroups->sortBy('updated_at', SORT_NATURAL, $sortDesc)->values();
            break;
         case 'created_at':
            $providerGroups = $providerGroups->sortBy('created_at', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $providerGroups = $this->paginate($providerGroups, $entries, $page);
         return response()->json($providerGroups);
      }

      $providerGroups = $providerGroups
         ->filter(function ($providerGroup) {
            if (!$providerGroup['has_translation']) return $providerGroup;
         })
         ->values();

      $providerGroups = $this->paginate($providerGroups, $entries, $page);

      return response()->json($providerGroups);
   }

   public function all(Request $request)
   {
      $lang = $request->lang;

      $providerGroups = ProviderGroup::all()->map(function ($providerGroup) use($lang){

         $providerGroup['name'] = getTranslation($providerGroup['name'], $lang);
     
         return $providerGroup;
     
      });

      return response()->json($providerGroups);
   }

   // for Category here
   public function store(StoreProviderGroupRequest $request)
   {
      $lang = $request->lang;
      // dd($lang);
      $providerGroupName = ucwords($request->provider_group_name);

      if (is_null($request->id)) {
         $providerGroup = ProviderGroup::create([
            "name"      => json_encode([
                        $lang => $providerGroupName,
                        ]),
         ]);

         if($request->image != null) {
            $image = $request->file('image');

            $this->uploadImage($providerGroup, $image);
        }

      } else {

         $providerGroup = ProviderGroup::with(['providers'])->findOrFail($request->id);

         $providerGroupVal = string_add_json( $lang, ucwords( $request -> provider_group_name ), string_remove( $lang, $providerGroup -> provider_group_name ) );

         $providerGroup->update([
            "name"      => $providerGroupVal,
         ]);

      }

      $providerGroup->name = $providerGroup->provider_group_name;

      return response()->json($providerGroup);
   }

   public function destroy(Request $request)
   {
      ProviderGroup::where('id',$request->id)->delete();

      return response()->json(true);
   }

   public function getProviderGroupName( Request $request ) {
      // dd($request->id());
      $providerGroup = ProviderGroup::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $providerGroup->name) );
      // dd($message);
      return response() -> json($message);

   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }

   public function getProviderGroup( Request $request ) {
      $lang= $request->lang;
      // dd($request->brandId);
      // $providersBrand = ProvidersBrand::where('provider', $request->id)->where('brands', $request->brandId)->get();
      // if($providersBrand->isEmpty()) return;
      $providerGroup = ProviderGroup::whereId( $request->id ) ->with([
         'providers'=> function($q1) use ($request) {
            $q1->whereHas('providers_brand_item', function($q2) use ($request) {
               $q2->where('brands', $request->brandId);
            });
         },
      ])->first();
      $previous = ProviderGroup::where('id', '<', $request->id)->max('id');

      // get next user id
      $next = ProviderGroup::where('id', '>', $request->id)->min('id');

      $providerGroup->providers = collect($providerGroup->providers)->map(function($provider) use($lang){
            $provider->name = getTranslation($provider->name, $lang);
            $provider->contact_no = json_decode($provider->contact_no);
            $provider->images = json_decode($provider->images);
         return $provider;
      });

      if($providerGroup) {
         $providerGroup->name = getTranslation($providerGroup->name, $lang);
         $providerGroup->previous = $previous;
         $providerGroup->next = $next;
      }

      return response() -> json($providerGroup);

   }

   public function addToProvider(Request $request)
   {
      $childID = (int) $request->child_id;
      $parentID = (int) $request->id;

      $provider = Provider::findOrFail($childID);
      
      if($provider->provider_sync_reference != null) {
         $provider->provider_sync_reference()->updateOrCreate([
            'table_id'   => $provider->id,
         ],[
            'sync_class' => 'Provider',
            'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'providers',
         ]);
      }

      if ($request->key == 'unlink') {
         Provider::withoutSyncingToSearch(function () use($provider) {
            $provider->group_id = null;
            $provider->update();
         });
         $providerGroup = ProviderGroup::with(['providers'])->findOrFail($parentID);
         $providerGroup->name = $providerGroup->provider_group_name;
         return response() -> json($providerGroup);
      }

      Provider::withoutSyncingToSearch(function () use($provider, $parentID) {
         $provider->group_id = $parentID;
         $provider->update();
      });
      
      $providerGroup = ProviderGroup::with(['providers'])->findOrFail($parentID);
      $providerGroup->name = $providerGroup->provider_group_name;
      return response() -> json($providerGroup);

   }

   public function uploadImage($providerGroup, $image)
   {
      $image = new ImageHelper([
         'driver' => 's3',
         'id' => $providerGroup->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'provider-groups-images',
         'file' => $image,
      ]);

      $image->deleteDirectory();

      if ($image->upload()) {
         $providerGroup->image =  $image->imageUrl();
         $providerGroup->save();
      }
   }
}
