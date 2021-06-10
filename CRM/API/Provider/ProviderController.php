<?php

namespace CRM\API\Provider;

use Artisan;
use DB;
use App\Http\Controllers\Controller;
use App\Models\Provider\ProviderActor;
use App\Models\Provider\ProviderAndItsType;
use App\Models\Provider;
use App\Models\ProvidersBrand;
use App\Models\Profile;
use App\Models\ContactType;
use App\Models\Contact;
use App\Models\SyncReference;
use App\Http\Requests\Backend\Provider\StoreProviderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Helpers\General\ImageHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use CRM\API\Provider\ProviderRepository;

class ProviderController extends Controller
{  
   protected $providerRepository;

   public function __construct(ProviderRepository $providerRepository)
   {
      $this->providerRepository = $providerRepository;
   }

   public function index(Request $request)
   {
      $lang = $request->lang;
      $brand_id = $request->brand_id;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $isSearched = $request->isSearched;
      
      $sortDesc = $request->sort_desc == 'true' ? true: false;
      $providers = Provider::select('id','name', 'country','city','division','contact_no','address','website', 'images', 'postal_code','facebook_url', 'linkedin','group_id', 'account_status', 'plan')->with([
        'providers_brand_item',
        'provider_group_item',
        'provider_actors',
        'country_item' => function($query) {
            $query->select('id','name');
         },
         'division_item' => function($query) {
            $query->select('id','name');
         },
         'city_item' => function($query) {
            $query->select('id','name');
         },
         'provider_profiles',
         'provider_services',
         'provider_and_its_type_items',
         'provider_and_its_types',
         'provider_sync_reference',
         'provider_other_infos',
         'payment_plan',
        ])->withCount(['provider_services']);
      //   $file = file_get_contents('provider-images/637/national-cancer-institute-KMvoHcB-w5g-unsplash.jpg');
      //   dd(base64_encode($file));
      if($brand_id != null) {
         $providers = $providers->where(function($subQuery) use ($brand_id){   
               $subQuery->whereHas('providers_brand_item', function ( $query ) use ($brand_id) {
                  $query->where('brands',  $brand_id);
               });
         });

         $providers->when($request->hasGroup, function ($q) use($request) {
            return $q->orderBy('group_id', 'DESC');
         });
         
         if($isSearched) {
            // dd($isSearched);
            $providers = $providers->where('name', 'LIKE', '%' . $search . '%');

            $providers->when($request->country != null, function ($q) use($request) {
               return $q->where('country', $request->country);
            })
            ->when($request->provinceOrDivision != null, function ($q) use($request) {
               return $q->where('division', $request->provinceOrDivision);
            })
            ->when($request->city != null, function ($q) use($request) {
               return $q->where('city', $request->city);
            })
            ->when($request->providerType != null, function ($q) use($request) {
               return $q->where(function($subQuery) use ($request) {
                  $subQuery->whereHas('provider_and_its_types', function ($query) use ($request) {
                     $query->where('type_of_provider', $request->providerType);
                  });
               });
               // return $q->where('provider_type', $request->providerType);
            })
            ->when($request->service != null, function ($q) use($request) {
               return $q->where(function($subQuery) use ($request) {
                  $subQuery->whereHas('provider_services', function ($query) use ($request) {
                     $query->where('services', $request->service);
                  });
               });
            })
            ->when($request->parameter != null, function ($q) use($request) {
               return $q->where(function($subQuery) use ($request) {
                  $subQuery->whereHas('provider_services', function ($query) use ($request) {
                     $query->where('parameter', $request->parameter);
                  });
               });
            })
            ->when($request->serviceType != null, function ($q) use($request) {
               
               return $q->where(function($subQuery) use ($request) {
                  $subQuery->whereHas('provider_services.service_item', function ($query) use ($request) {
                     $query->whereJsonContains('medical_terms', (int)$request->serviceType);
                  });
               });
            });
         }

         if($request->filterProviderBy || $request->filterProviderBy != 'all') {
            switch ($request->filterProviderBy) {
               case 'nsa':
                  $providers = $providers->doesntHave('provider_services');
                  break;
               case 'nrfas':
                  $providers = $providers->doesntHave('provider_sync_reference')->has('provider_and_its_type_items');
                  break;
               case 'npt':
                  $providers = $providers->doesntHave('provider_and_its_type_items');
                  break;
            }
         };
      }
      
      $providers = $providers->paginate($entries);

      return $providers;
      $providers = $providers->map(function ($provider) use($lang){
         return [
               'id' => $provider['id'], 
               'name' => getTranslation($provider['name'],$lang), 
               'created_at' => $provider['created_at']->toDateString(), 
               'updated_at' => $provider['updated_at']->toDateString(), 
               'group_id' => $provider['group_id'], 
               'address' => $provider['address'], 
               'contact_no' => json_decode($provider['contact_no']), 
               'postal_code' => $provider['postal_code'], 
               'facebook_url' => $provider['facebook_url'],
               'linkedin' => $provider['linkedin'],
               'website' => $provider['website'], 
               'providers_brand_item' => $provider['providers_brand_item'],
               'provider_actors' => $provider['provider_actors'],
               'brands' => $provider['selected_brands'],
               'account_status' => $provider['account_status'],
               'provider_group_item'  => $provider['provider_group_item'] == null ? null : [
                  'id' => $provider['provider_group_item']['id'],
                  'name' => getTranslation($provider['provider_group_item']['name'],$lang),
               ],
               'country_item'  => $provider['country_item'] == null ? null : [
                  'id' => $provider['country_item']['id'],
                  'name' => $provider['country_item']['name'],
               ],
               'division_item'  => $provider['division_item'] == null ? null : [
                  'id' => $provider['division_item']['id'],
                  'name' => $provider['division_item']['name'],
               ],
               'city_item'  => $provider['city_item'] == null ? null : [
                  'id' => $provider['city_item']['id'],
                  'name' => $provider['city_item']['name'],
               ],
               'provider_profiles' => $provider['provider_profiles'] == null ? null : [
                  'provider' => $provider['provider_profiles']['provider'],
                  'profile' => $provider['provider_profiles']['profile'],
               ],
               'provider_sync_reference' => $provider['provider_sync_reference'],
               'provider_services_count' => $provider['provider_services_count'],
               'provider_services' => $provider['provider_services'],
               'provider_and_its_types' => $provider['provider_and_its_type_items']
         ];
      });
      // dd( $providers);
      switch ($request->sort_name) {
         case 'name':
            $providers = $providers->sortBy('name', SORT_NATURAL, $sortDesc)->values();
            // dd($providers);
            break;
         case 'province':
            $providers = $providers->sort(function ($a, $b) use($sortDesc){
               if (!$a['division_item']) {
                   return !$b['division_item'] ? 0 : 1;
               }
               if (!$b['division_item']) {
                   return -1;
               }
               if ($a['division_item'] == $b['division_item']) {
                   return 0;
               }
               if($sortDesc == true) {
                  return $a['division_item'] > $b['division_item'] ? -1 : 1;
               }
               return $a['division_item'] < $b['division_item'] ? -1 : 1;
            })->values();
            break;
         case 'city':
            $providers = $providers->sort(function ($a, $b) use($sortDesc){
               if (!$a['city_item']) {
                   return !$b['city_item'] ? 0 : 1;
               }
               if (!$b['city_item']) {
                   return -1;
               }
               if ($a['city_item'] == $b['city_item']) {
                   return 0;
               }
               if($sortDesc == true) {
                  return $a['city_item'] > $b['city_item'] ? -1 : 1;
               }
               return $a['city_item'] < $b['city_item'] ? -1 : 1;
            })->values();
            break;
         case 'services_count':
            $providers = $providers->sortBy('provider_services_count', SORT_NATURAL, $sortDesc)->values();
            break;
      }

      $providers = $this->paginate($providers, $entries, $page);
      
      return response()->json($providers);
   } 

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);
      
      return response()->json($brand);
   }

   // for Category here
   public function store(StoreProviderRequest $request)
   {
      $provider = null;
    
         $lang = $request->lang;
         $providerName = ucwords($request->name);
         if (is_null($request->id)) {
            Provider::withoutSyncingToSearch(function () use($request, &$provider, $lang, $providerName){
               $provider = new Provider;

               $provider->name = json_encode([
                  $lang => $providerName,
                  ]);
               $provider->country = $request->country;
               $provider->division = $request->division;
               $provider->city = $request->city;
               $provider->address = $request->address;
               $provider->contact_no = $request->contact_no;
               $provider->website = $request->website;
               $provider->facebook_url = $request->facebook_url;
               $provider->linkedin = $request->linkedin;
               $provider->postal_code = $request->postal_code;
               $provider->created_by = auth()->user()->id;
               $provider->last_modified_by = auth()->user()->id;
               $provider->plan = $request->plan;
               $provider->save();
      
               $provider->provider_sync_reference()->create([
                  'sync_class' => 'Provider',
                  'action' => 'New',
                  'source_table' => 'providers',
               ]);
      
               foreach(json_decode($request->provider_type, true) as $provider_type) {
                  $provider->provider_and_its_types()->create([
                     'type_of_provider' => $provider_type['id']
                  ]);
               }
      
               $address = $request->address == null ? 'None' : $request->address;
               $notes = [
                  "date_requested" => date("Y-m-d H:i:s"),
                  "index" => 0,
                  "notes" => "None",
                  "date_added" => date("Y-m-d H:i:s"),
                  "created_by" => auth()->user()->id,
               ];
      
               $notesArr = array();
               array_push($notesArr, $notes);
               $profile = profile::create([
                  'surname' => $providerName,
                  'notes' => json_encode($notesArr),
               ]);
      
               if($request->facebook_url != null) {
                  $contactType = ContactType::where('type_name', 'LIKE', '%'. 'FACEBOOK' .'%')->first();
                  if(!$contactType) {
                     $contactType = ContactType::create([
                        "type_name" => json_encode([
                                       'en' => 'Facebook',
                                       ]),
                     ]);
                  }
      
                  $contactType->contacts()->create([
                     'contact_info' => $request->facebook_url,
                     'created_by' => auth()->user()->id,
                     'profile_id' => $profile->id,
                  ]);
               }

               
               if($request->other_info != null) {
                  foreach (json_decode($request->other_info) as $otherInfo) {
                     $provider->provider_other_infos()->create([
                        'type_of_info' => $otherInfo->type,
                        'value' => $otherInfo->value,
                     ]);
                  }
               }
                  
               if($request->website != null) {
                  $contactType = ContactType::where('type_name', 'LIKE', '%'. 'WEBSITE' .'%')->first();
                  if(!$contactType) {
                     $contactType = ContactType::create([
                        "type_name" => json_encode([
                                       'en' => 'Website',
                                       ]),
                     ]);
                  }
      
                  $contactType->contacts()->create([
                     'contact_info' => $request->website,
                     'created_by' => auth()->user()->id,
                     'profile_id' => $profile->id,
                  ]);
               }
      
               if($request->contact_no != null) {
                  $contactType = ContactType::where('type_name', 'LIKE', '%'. 'Phone Number' .'%')->first();
                  if(!$contactType) {
                     $contactType = ContactType::create([
                        "type_name" => json_encode([
                                       'en' => 'Phone Number',
                                       ]),
                     ]);
                  }
                  foreach(json_decode($request->contact_no) as $contact_no) {
                     $contactType->contacts()->create([
                        'contact_info' => $contact_no,
                        'created_by' => auth()->user()->id,
                        'profile_id' => $profile->id,
                     ]);
                  }
               }
      
               if($request->country != null) {
                  $profile->client_location()->create([
                     'world_cities_id'       => $request->city,
                     'world_provinces_id'    => $request->division,
                     'world_countries_id'    => $request->country,
                  ]);
               }
      
               $profile->profile_brands()->create([
                  'brand_id' => $request->brand_id
               ]);
      
               $provider->provider_profiles()->create([
                  'profile' => $profile->id,
               ]);
      
               $provider->providers_brand_item()->create([
                  "brands"  => $request->brand_id,
               ]);

               $this->providerRepository->updateGeolocalizationAlgolia($request->country, $request->division, $request->city);
               
               if($request->images != null) $this->uploadImages($provider, $request->file('images'));

            });
           
   
         } else {
            $providerTypesArr = array();
            Provider::withoutSyncingToSearch(function () use($request, &$provider, $lang, $providerName, &$providerTypesArr){
               $uniqueID = [];
               $duplicates = [];

               $provider = $this->providerRepository->findProvider($request->id);

               $provider->provider_and_its_types->map(function($providerAndItsType) use (&$uniqueID, &$duplicates) {
                  $id = $providerAndItsType->type_of_provider;
                  if (in_array($id, $uniqueID)) {
                     // id is a duplicate
                     $duplicates[] = $providerAndItsType->id;
                  } else {
                     $uniqueID[] = $id;
                  }
               });
               DB::table('provider_and_its_type')->whereIn('id', $duplicates)->delete();
               $providerVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $provider -> name ) );
      
               $provider->name = json_encode([$lang => $providerName]);
               $provider->country = $request->country;
               $provider->division = $request->division;
               $provider->city = $request->city;
               $provider->address = $request->address;
               $provider->contact_no = $request->contact_no;
               $provider->website = $request->website;
               $provider->facebook_url = $request->facebook_url;
               $provider->linkedin = $request->linkedin;
               $provider->postal_code = $request->postal_code;
               $provider->created_by = auth()->user()->id;
               $provider->last_modified_by = auth()->user()->id;
               $provider->plan = $request->plan;
               $provider->update();
               $provider->provider_other_infos()->delete();
               foreach(json_decode($request->provider_type, true) as $provider_type) {
                  array_push($providerTypesArr, $provider_type['id']);
               }

               if($request->other_info != null) {
                  foreach (json_decode($request->other_info) as $otherInfo) {
                     $provider->provider_other_infos()->create([
                        'type_of_info' => $otherInfo->type,
                        'value' => $otherInfo->value,
                     ]);
                  }
               }

               $providerTypesToAdd = array_diff($providerTypesArr, $provider->provider_and_its_types->pluck('type_of_provider')->toArray());
               foreach($providerTypesToAdd as $providerType) {
                  $provider->provider_and_its_types()->create([
                     'type_of_provider' => $providerType
                  ]);
               }
               if($provider->provider_sync_reference != null) {
                  $provider->provider_sync_reference()->updateOrCreate([
                     'table_id'   => $provider->id,
                  ],[
                     'sync_class' => 'Provider',
                     'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
                     'source_table' => 'providers',
                  ]);
               }
               
               $this->providerRepository->updateGeolocalizationAlgolia($request->country, $request->division, $request->city);

               if($request->images != null) $this->uploadImages($provider, $request->file('images'));
            
            });
            $providerTypesToDelete = array_diff($provider->provider_and_its_types->pluck('type_of_provider')->toArray(), $providerTypesArr);
            foreach ($providerTypesToDelete as $providerType) {
               ProviderAndItsType::where('provider', $provider->id)->where('type_of_provider', $providerType)->first()->delete();
            }

            $provider = $this->providerRepository->findProvider($request->id);

         }
   
      
      return response()->json($provider);
   }

   public function linkBrand(Request $request)
   {
      $lang = $request->lang;

      $request->validate([
            'brands'         => 'required',
         ],
         [
            'brands.required'    => 'Brand Name is Required',
         ]
      );

      $provider = $this->providerRepository->findProvider($request->id);

      $provider->providers_brand_item()->delete();

      foreach(json_decode($request->brands) as $value){
         $provider->providers_brand_item()->create([
            "brands"  => $value,
         ]);
      };

      return response()->json($provider);
   }
   
   public function linkActor(Request $request)
   {
      // dd($request->key);
      if ($request->key === 'link') {
         $providerActor = new ProviderActor;

         $providerActor->provider = $request->id;
         $providerActor->actor    = $request->actor_id;

         $providerActor->save();

      } else {
         $providerActor = ProviderActor::where('provider', $request->id)->where('actor', $request->actor_id)->delete();

      }
      $provider = $this->providerRepository->findProvider($request->id);

      if($provider->provider_sync_reference != null) {
         $provider->provider_sync_reference()->updateOrCreate([
            'table_id'   => $provider->id,
         ],[
            'sync_class' => 'Provider',
            'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'providers',
         ]);
      }
      
      return response()->json($provider);
   }

   public function addToGroup(Request $request)
   {
      $lang = $request->lang;
      $provider = Provider::withoutSyncingToSearch(function () use($request, $lang){
         $provider = Provider::findOrFail($request->id);
         $provider->group_id = $request->provider_group;
         $provider->update();

         $provider = $this->providerRepository->findProvider($request->id);

         return $provider;
      });
      
      if($provider->provider_sync_reference != null) {
         $provider->provider_sync_reference()->updateOrCreate([
            'table_id'   => $provider->id,
         ],[
            'sync_class' => 'Provider',
            'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'providers',
         ]);
      }
      return response()->json($provider);
   }

   public function changeAccountStatus(Request $request)
   {
      $lang = $request->lang;
      $provider = Provider::withoutSyncingToSearch(function () use($request, $lang){

         $provider = $this->providerRepository->findProvider($request->id);
         $provider->account_status = $request->account_status;
         $provider->update();
         return $provider;

      });

      if($provider->provider_sync_reference != null) {
         $provider->provider_sync_reference()->updateOrCreate([
            'table_id'   => $provider->id,
         ],[
            'sync_class' => 'Provider',
            'action' => $provider->provider_sync_reference->action != 'New' ? 'Update' : 'New',
            'source_table' => 'providers',
         ]);
      }
      
      return response()->json($provider);
   }

   public function syncToAlgolia(Request $request)
   {
      
      $brand = $request->brand;

      $providers = Provider::with(['providers_brand_item', 'provider_and_its_type_items'])
                     ->whereHas('provider_sync_reference', function($q){
                        $q->where('action', '!=', 'Added');
                     })
                     ->whereHas('providers_brand_item', function ( $query ) use ($brand) {
                        $query->where('brands',  $brand['id']);
                     })->get();

      request()->merge(["brand_name" => $brand['name']]);

      foreach ($providers as $provider) {
         $uniqueID = [];
         $duplicates = [];
         ProviderAndItsType::where('provider', $provider->provider_sync_reference->table_id)->get()
               ->map(function(ProviderAndItsType $providerAndItsType) use (&$uniqueID, &$duplicates) {
                  $id = $providerAndItsType->type_of_provider;
                  if (in_array($id, $uniqueID)) {
                     // id is a duplicate
                     $duplicates[] = $providerAndItsType->id;
                  } else {
                     $uniqueID[] = $id;
                  }
               });
         DB::table('provider_and_its_type')->whereIn('id', $duplicates)->delete();
         $provider->searchable();
         $provider->provider_sync_reference->action = 'Added';
         $provider->provider_sync_reference->update();
      }
      
      $noProviderAndItsTypeCount = 0;
      foreach ($providers as $provider) {
         if($provider->provider_and_its_type_items->isEmpty()) {
            $noProviderAndItsTypeCount++;
         };
      }
      return response()->json($noProviderAndItsTypeCount);
   }

   public function addToSyncReference(Request $request)
   {
      $lang = $request->lang;
      $provider = Provider::with([
         'provider_sync_reference',
      ])->findOrFail($request->id);
      $provider->provider_sync_reference()->updateOrCreate([
         'table_id'   => $provider->id,
      ],[
         'sync_class' => 'Provider',
         'action' => 'New',
         'source_table' => 'providers',
      ]);
      
      $provider = $this->providerRepository->findProvider($request->id);
      
      return response()->json($provider);
   }

   public function destroy(Request $request)
   {
 
      $provider = Provider::find($request->id);
      if($provider->provider_sync_reference != null) {
         $providerTypes = $provider->provider_and_its_types->pluck('type_of_provider')->toArray();
         foreach ($providerTypes as $providerType) {
            ProviderAndItsType::where('provider', $provider->id)->where('type_of_provider', $providerType)->first()->delete();
         }
         $provider->provider_sync_reference()->delete();
      };
      $provider->delete();

      return response()->json(true);
   }

   public function getproviderName( Request $request ) 
   {
      // dd($request->id());
      $provider = Provider::whereId( $request->id ) -> first();
      // dd($organizationCategory);
      $message = ucwords( string_to_value( $request->lang , $provider->name) );
      // dd($message);
      return response() -> json($message);


   }

   public function getAlgoliaSummary( Request $request ) 
   {
      $brandID = $request->brand_id;

      $newReferences = SyncReference::with(['provider'])
                        ->where(function($subQuery) use ($brandID){   
                           $subQuery->whereHas('provider.providers_brand_item', function ( $query ) use ($brandID) {
                              $query->where('brands',  $brandID);
                           });
                        })
                        ->where('source_table', 'providers')
                        ->where('action', 'New')
                        ->count();

      $updateReferences = SyncReference::with(['provider'])
                           ->where(function($subQuery) use ($brandID){   
                              $subQuery->whereHas('provider.providers_brand_item', function ( $query ) use ($brandID) {
                                 $query->where('brands',  $brandID);
                              });
                           })
                           ->where('source_table', 'providers')
                           ->where('action', 'Update')
                           ->count();

      $summary = [
         'new' => $newReferences,
         'update' => $updateReferences,
      ];

      return response() -> json($summary);

   }

   public function getProvider( Request $request ) {
      $lang= $request->lang;
      // dd($request->brandId);
      $providersBrand = ProvidersBrand::where('provider', $request->id)->where('brands', $request->brandId)->get();
      // dd($providersBrand);
      if($providersBrand->isEmpty()) {
         $provider = ProvidersBrand::where('brands', $request->brandId)->get();
         // dd($provider);
         if($provider->isEmpty()) return;
         $provider = ProvidersBrand::where('brands', $request->brandId)->first();
         $provider = Provider::whereId( $provider->provider ) ->with([
            'provider_and_its_type_items',
            'country_item' => function($query) {
               $query->select('id','name');
            },
            'division_item' => function($query) {
               $query->select('id','name');
            },
            'city_item' => function($query) {
               $query->select('id','name');
            },
            ])->withCount(['provider_services'])-> first();
            $previous = Provider::with(['providers_brand_item'])
            ->where(function ($subQuery) use ($request) {
               $subQuery->whereHas('providers_brand_item', function ($query) use ($request) {
                  $query->where('brands',  $request->brandId);
               });
            })->where('id', '<', $provider->id)->max('id');

      // get next user id
            $next = Provider::with(['providers_brand_item'])
            ->where(function ($subQuery) use ($request) {
               $subQuery->whereHas('providers_brand_item', function ($query) use ($request) {
                  $query->where('brands',  $request->brandId);
               });
            })->where('id', '>', $provider->id)->min('id');
      } else {
         $provider = Provider::whereId( $request->id ) ->with([
            'provider_and_its_type_items',
            'country_item' => function($query) {
               $query->select('id','name');
            },
            'division_item' => function($query) {
               $query->select('id','name');
            },
            'city_item' => function($query) {
               $query->select('id','name');
            },
            ])->withCount(['provider_services'])-> first();

            $previous = Provider::with(['providers_brand_item'])
            ->where(function ($subQuery) use ($request) {
               $subQuery->whereHas('providers_brand_item', function ($query) use ($request) {
                  $query->where('brands',  $request->brandId);
               });
            })->where('id', '<', $request->id)->max('id');

      // get next user id
            $next = Provider::with(['providers_brand_item'])
            ->where(function ($subQuery) use ($request) {
               $subQuery->whereHas('providers_brand_item', function ($query) use ($request) {
                  $query->where('brands',  $request->brandId);
               });
            })->where('id', '>', $request->id)->min('id');
      }
   

   

      if($provider) {
         $provider->name = getTranslation($provider->name, $lang);
         $provider->country_item =$provider->country_item;
         $provider->division_item =$provider->division_item;
         $provider->city_item =$provider->city_item;
         $provider->images = json_decode($provider->images);
         $provider->provider_profiles = $provider->provider_profiles;
         $provider->provider_profiles->provider_profile = $provider->provider_profiles->provider_profile;
         $provider->provider_and_its_types = $provider->provider_and_its_type_items;
         $provider->previous = $previous;
         $provider->next = $next;
      }
      // dd($provider);
      return response() -> json($provider);

   }

   public function addProviderProfile( Request $request ) 
   {
      $lang= $request->lang;
      $provider = Provider::whereId( $request->id ) ->with([
         'provider_type_item',
         'country_item' => function($query) {
             $query->select('id','name');
          },
          'division_item' => function($query) {
             $query->select('id','name');
          },
          'city_item' => function($query) {
             $query->select('id','name');
          },
          'provider_profiles',
         ])->withCount(['provider_services'])-> first();
      // dd();
      $address = $request->address == null ? 'None' : $request->address;
      $notes = [
         "date_requested" => date("Y-m-d H:i:s"),
         "index" => 0,
         "notes" => "Address: {$address}",
         "date_added" => date("Y-m-d H:i:s"),
         "created_by" => auth()->user()->id,
      ];

      $notesArr = array();
      array_push($notesArr, $notes);
      $profile = profile::create([
         'surname' => getTranslation($provider->name, 'en'),
         'notes' => json_encode($notesArr),
      ]);

      $profile->profile_brands()->create([
         'brand_id' => $request->brand_id
      ]);

      if($provider->country != null) {
         $profile->client_location()->create([
            'world_cities_id'       => $provider->city,
            'world_provinces_id'    => $provider->division,
            'world_countries_id'    => $provider->country,
         ]);
      }

      if($provider->facebook_url != null) {
         $contactType = ContactType::where('type_name', 'LIKE', '%'. 'FACEBOOK' .'%')->first();
         if(!$contactType) {
            $contactType = ContactType::create([
               "type_name" => json_encode([
                              'en' => 'Facebook',
                              ]),
            ]);
         }

         $contactType->contacts()->create([
            'contact_info' => $provider->facebook_url,
            'created_by' => auth()->user()->id,
            'profile_id' => $profile->id,
         ]);
      }
         
      if($provider->website != null) {
         $contactType = ContactType::where('type_name', 'LIKE', '%'. 'WEBSITE' .'%')->first();
         if(!$contactType) {
            $contactType = ContactType::create([
               "type_name" => json_encode([
                              'en' => 'Website',
                              ]),
            ]);
         }

         $contactType->contacts()->create([
            'contact_info' => $provider->website,
            'created_by' => auth()->user()->id,
            'profile_id' => $profile->id,
         ]);
      }

      if($provider->contact_no != null && count(json_decode($provider->contact_no)) != 0) {
         $contactType = ContactType::where('type_name', 'LIKE', '%'. 'Phone Number' .'%')->first();
         if(!$contactType) {
            $contactType = ContactType::create([
               "type_name" => json_encode([
                              'en' => 'Phone Number',
                              ]),
            ]);
         }
         foreach(json_decode($provider->contact_no) as $contact_no) {
            $contactType->contacts()->create([
               'contact_info' => $contact_no,
               'created_by' => auth()->user()->id,
               'profile_id' => $profile->id,
            ]);
         }
      }

      $provider->provider_profiles()->create([
         'profile' => $profile->id,
      ]);

      $provider->providers_brand_item()->create([
         "brands"  => $request->brand_id,
      ]);

      if($provider) {
         $provider->name = getTranslation($provider->name, $lang);
         $provider->provider_type_item->name = getTranslation($provider->provider_type_item->name, $lang);
         $provider->country_item =$provider->country_item;
         $provider->division_item =$provider->division_item;
         $provider->city_item =$provider->city_item;
         $provider->contact_no = json_decode($provider->contact_no);
         $provider->images = json_decode($provider->images);
         $provider->provider_profiles = $provider->provider_profiles;
      }
      // dd($provider);
      return response() -> json($provider);
      
   }

   public function uploadImages($provider, $images)
   {           
      $betaImage = new ImageHelper([
         'driver' => 's3',
         'id' => $provider->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'provider-images/',
         'file' => $images[0],
      ]);

      $imagesUrlArr = array();

      foreach($images as $image) {
         $finalImage = new ImageHelper([
            'driver' => 's3',
            'id' => $provider->id,
            's3_storage_path' => 'med4care-customercare',
            's3_folder_path' => 'provider-images',
            'file' => $image,
         ]);

         $finalImage->upload();
      }
      
      $provider->images = $betaImage->getAllFiles();
      $provider->save();
   }

   public function deleteImage(Request $request)
   {          
      $explodedImage = explode("/", $request->image);
      
      $betaImage = new ImageHelper([
         'driver' => 's3',
         'id' => $request->id,
         's3_storage_path' => 'med4care-customercare',
         's3_folder_path' => 'provider-images/',
         'file' => end($explodedImage),
      ]);

      $betaImage->deleteImage(end($explodedImage));
      $provider = Provider::findOrFail($request->id);
      $provider->images = $betaImage->getAllFiles();
      $provider->save();
      $provider = $this->providerRepository->findProvider($request->id);
   
      return response()->json($provider);
   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }

   public function compareOtherInfos($objA, $objB) {
      dd($objA);
      return $objA->getId() <=> $objB->getId();
   }
}
