<?php

namespace CRM\API\Actor;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use App\Models\Brand;
use App\Models\ActorSpecialization;
use App\Models\ActorTerm;
use App\Models\Actors\ActorProfession;
use App\Http\Requests\Backend\Actor\StoreActorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\General\ImageHelper;

class ActorController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;
      $actorSearch = $request->actor_search;
      $brand_id = $request->brand_id;
      $sortDesc = $request->sort_desc == 'true' ? true : false;
      $entries = $request->entries;
      $page = (int)$request->page;
      $search = $request->search;
      $actors = Actor::select('id', 'firstname', 'surname', 'middlename', 'created_at', 'updated_at', 'physical_code', 'other_info')->with([
         'actors_brand_item',
         'actor_terms',
         'actor_terms',
         'actor_field_of_professions',
         'actor_field_of_professions_items',
      ]);
        
      if ($search != null) {
         $actors  =  $actors->where(function ($query) use($search){
                        $query->whereRaw("concat(surname, ' ', firstname) like '%$search%' ")
                        ->orWhereRaw("concat(surname, ' ', firstname, ' ', middlename) like '%$search%' ")
                        ->orWhere('surname', 'LIKE', '%' . $search . '%')
                        ->orWhere('firstname', 'LIKE', '%' . $search . '%')
                        ->orWhere('middlename', 'LIKE', '%' . $search . '%')
                        ->orWhere('physical_code', 'LIKE', '%' . $search . '%')
                        ->orWhere('other_info', 'LIKE', '%' . $search . '%');
                     });
      }

      
      $actors = $actors->where(function ($subQuery) use ($brand_id) {
         $subQuery->whereHas('actors_brand_item', function ($query) use ($brand_id) {
            $query->where('brands',  $brand_id);
         });
      });
      
     
      $actors = $actors->get();


      $actors = $actors->map(function ($actor) use ($lang) {
         // dd($actor->actor_specialization);
         $physicalCode = null;
         $specialization = null;
         $otherInfo = null;
         $specializationCategory = null;
         if ($actor->physical_code_types != null) {
            $physicalCode = collect($actor->physical_code_types)->map(function ($physicalCode) use ($lang) {
               return [
                  'code'   => $physicalCode['code'],
                  'index'  => $physicalCode['index'],
                  'physical_code_type' => $physicalCode['physical_code_type'],
                  'physical_code_and_description' => $physicalCode['physical_code_type']->map(function ($physicalCodeType) use ($lang, $physicalCode) {
                     // dd($physicalCodeType);
                     $name = $physicalCodeType->physical_code_type_name;
                     $code = $physicalCode['code'];
                     $physicalCodeType->typeAndCode = "{$name}: {$code}";
                     return $physicalCodeType->typeAndCode;
                  })
               ];
            });
         }
         // if (count($actor->actor_specialization)) {
         //    $specialization = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
         //       // dd($specialization->toArray());
         //       $specialization->specialization_item->profession_description = "{$specialization->profession_item->select_profession_name}: {$specialization->specialization_item->select_description_name}";
         //       $specialization->specialization_item->job_category_id = $specialization->category_item->id;
         //       return [
         //          'index'  => $key,
         //          'profession' => $specialization->specialization_item,
         //          'specialization' => '',
         //       ];
         //    });

         //    $specializationCategory = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
         //       return $specialization->category_item->category_name;
         //    });

         //    $specializationCategory = array_unique($specializationCategory->toArray());
         // }
         if ($actor->other_info != null) {
            $otherInfo = collect($actor->other_infos)->map(function ($otherInfo) use ($lang) {
               return [
                  'index'  => $otherInfo['index'],
                  'type'   => $otherInfo['type'],
                  'value'  => $otherInfo['value'],
               ];
            });
         }
         return [
            'id' => $actor->id,
            'fullname' => $actor->full_name,
            'full_name' => $actor->full_name, // this is for article please dont delete
            'firstname' => $actor->firstname,
            'surname' => $actor->surname,
            'middlename' => $actor->middlename,
            'physical_code' => $actor->physical_code,
            'physical_code_item' => $physicalCode,
            'other_info_item' => $otherInfo,
            'actor_field_of_professions' => $actor->actor_field_of_professions,
            'actor_field_of_professions_items' => $actor->actor_field_of_professions_items,
            'actor_terms' => $actor->actor_terms,
            'brands' => $actor->selected_brands,
            'medical_terms' => $actor->medical_terms,
            'created_at' => $actor->created_at->toDateTimeString(),
            'updated_at' => $actor->updated_at->toDateTimeString(),
         ];
      });

      if ($entries != null && $page != null) {
         $actors = $this->paginate($actors, $entries, $page);
      }

      return response()->json($actors);
   }

   public function all(Request $request)
   {
      // dd('test');
      $lang = $request->lang;
      $brand_id = $request->brand_id;
      $search = $request->search;
      $actors = Actor::select('id', 'firstname', 'surname', 'middlename', 'physical_code', 'other_info')->with([
         'actors_brand_item',
         'actor_specialization.category_item',
         'actor_specialization.profession_item',
         'actor_specialization.specialization_item',
      ]);

      $brand = Brand::findOrFail($brand_id);
      // return response()->json();
      if ($search != null) {
         $actors  =  $actors->where(function ($query) use($search){
                        $query->whereRaw("concat(surname, ' ', firstname) like '%$search%' ")
                        ->orWhereRaw("concat(surname, ' ', firstname, ' ', middlename) like '%$search%' ")
                        ->orWhere('surname', 'LIKE', '%' . $search . '%')
                        ->orWhere('firstname', 'LIKE', '%' . $search . '%')
                        ->orWhere('middlename', 'LIKE', '%' . $search . '%')
                        ->orWhere('physical_code', 'LIKE', '%' . $search . '%')
                        ->orWhere('other_info', 'LIKE', '%' . $search . '%')
                        ->orWhere(function ($subQuery) use ($search) {
                           // $query->whereRaw("concat(last_name, ' ', first_name) like '%$search%' ");
                           $subQuery->whereHas('actor_specialization.category_item', function ($query) use ($search) {
                              $query->where('category',  'LIKE', '%' . $search . '%');
                           })
                              ->orWhereHas('actor_specialization.profession_item', function ($query) use ($search) {
                                 $query->where('profession',  'LIKE', '%' . $search . '%');
                              })
                              ->orWhereHas('actor_specialization.specialization_item', function ($query) use ($search) {
                                 $query->where('description',  'LIKE', '%' .  $search . '%');
                              });
                        });
                     });
      }

      // $actors = $actors->where(function ($subQuery) use ($brand_id) {
      //    $subQuery->whereHas('actors_brand_item', function ($query) use ($brand_id) {
      //       $query->where('brands',  $brand_id);
      //    });
      // })->where(function ($subQuery) use ($brand) {
      //    $subQuery->whereHas('actor_specialization.category_item', function ($query) use ($brand) {
      //       $query->whereIn('id',  $brand->brand_categories->pluck('category'));
      //    });
      // });
      
     
      $actors = $actors->get();

      // dd($actors);


      $actors = $actors->map(function ($actor) use ($lang) {
         // dd($actor->actor_specialization);
         // $specialization = null;
         // $specializationCategory = null;
         // if (count($actor->actor_specialization)) {
         //    $specialization = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
         //       // dd($specialization->toArray());
         //       $specialization->specialization_item->profession_description = "{$specialization->profession_item->select_profession_name}: {$specialization->specialization_item->select_description_name}";
         //       $specialization->specialization_item->job_category_id = $specialization->category_item->id;
         //       return [
         //          'index'  => $key,
         //          'profession' => $specialization->specialization_item,
         //          'specialization' => '',
         //       ];
         //    });

         //    $specializationCategory = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
         //       // dd($specialization->toArray());
         //       return $specialization->category_item->category_name;
         //    });

         //    $specializationCategory = array_unique($specializationCategory->toArray());

         //    // dd($specializationCategory);
         // }
         return [
            'id' => $actor->id,
            'fullname' => $actor->full_name,
            'full_name' => $actor->full_name, // this is for article please dont delete
            'firstname' => $actor->firstname,
            'surname' => $actor->surname,
            'middlename' => $actor->middlename,
            'actor_field_of_professions_items' => $actor->actor_field_of_professions_items,
            // 'specialization_item' => $specialization,
            // 'specialization_category' => $specializationCategory,
         ];
      });

      return response()->json($actors);
   }

   public function show(Request $request, $id)
   {
      $brand = $this->brandRepository->getById($id);

      return response()->json($brand);
   }

   // for Category here
   public function store(StoreActorRequest $request)
   {
      $lang = $request->lang;
      $physicalCode = null;
      $otherInfo = null;

      if ($request->physical_code != null) $physicalCode = json_encode($request->physical_code);

      if ($request->other_info != null) $otherInfo = json_encode($request->other_info);

      if (is_null($request->id)) {
         $actor = new Actor;

         $actor->firstname          = $request->firstname;
         $actor->surname            = $request->surname;
         $actor->middlename         = $request->middlename;
         $actor->physical_code      = $physicalCode;
         $actor->other_info         = $otherInfo;
         $actor->save();

         if (count($request->linked_medical_terms) != 0) {
            foreach ($request->linked_medical_terms as $term) {
               $actor->actor_terms()->create([
                  "term"  => $term,
               ]);
            };
         }

         if($request->field_of_professions) {
            foreach ($request->field_of_professions as $professionID) {
               $actor->actor_field_of_professions()->create([
                  "profession"  => $professionID,
               ]);
            };
         }

         $actor->actors_brand_item()->create([
            "brands"  => $request->brand_id,
         ]);
         $actor->get();
      } else {

         $actor = Actor::findOrFail($request->id);
         $actor->firstname          = $request->firstname;
         $actor->surname            = $request->surname;
         $actor->middlename         = $request->middlename;
         $actor->physical_code      = $physicalCode;
         $actor->other_info         = $otherInfo;
         $actor->update();

         foreach (ActorTerm::where('actor', $request->id)->pluck('id') as $id) {
            $actorTerm = ActorTerm::find($id);
            $actorTerm->delete();
         }

         if (count($request->linked_medical_terms) != 0) {
            foreach ($request->linked_medical_terms as $term) {
               $actor->actor_terms()->create([
                  "term"  => $term,
               ]);
            };
         }

         if($request->field_of_professions) {
            $actorFieldOfProfessionsToAdd = array_diff($request->field_of_professions, $actor->actor_field_of_professions->pluck('profession')->toArray());
            foreach($actorFieldOfProfessionsToAdd as $fieldOfProfession) {
               $actor->actor_field_of_professions()->create([
                  'profession' => $fieldOfProfession
               ]);
            }

            $actorFieldOfProfessionsToDelete = array_diff($actor->actor_field_of_professions->pluck('profession')->toArray(), $request->field_of_professions);
            foreach ($actorFieldOfProfessionsToDelete as $fieldOfProfession) {
               ActorProfession::where('actor', $actor->id)->where('profession', $fieldOfProfession)->first()->delete();
            }
         } else {
            ActorProfession::where('actor', $actor->id)->delete();
         }
         

         $actor = Actor::with([
            'actors_brand_item',
            'actor_specialization.category_item',
            'actor_specialization.profession_item',
            'actor_specialization.specialization_item',
            'actor_field_of_professions',
            'actor_field_of_professions_items',
            'actor_terms',
         ])->findOrFail($request->id);
      }

      $physicalCode = null;
      $specialization = null;
      $otherInfo = null;
      $specializationCategory = null;
      if ($actor->physical_code_types != null) {
         $physicalCode = collect($actor->physical_code_types)->map(function ($physicalCode) use ($lang) {
            return [
               'code'   => $physicalCode['code'],
               'index'  => $physicalCode['index'],
               'physical_code_type' => $physicalCode['physical_code_type'],
               'physical_code_and_description' => $physicalCode['physical_code_type']->map(function ($physicalCodeType) use ($lang, $physicalCode) {
                  // dd($physicalCodeType->name);
                  $name = $physicalCodeType->physical_code_type_name;
                  $code = $physicalCode['code'];
                  $physicalCodeType->typeAndCode = "{$name}: {$code}";
                  return $physicalCodeType->typeAndCode;
               })
            ];
         });
      }
      if (count($actor->actor_specialization)) {
         $specialization = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
            // dd($specialization);
            $specialization->specialization_item->profession_description = "{$specialization->profession_item->profession_name}: {$specialization->specialization_item->description_name}";
            $specialization->specialization_item->job_category_id = $specialization->category_item->id;
            return [
               'index'  => $key,
               'profession' => $specialization->specialization_item,
               'specialization' => '',
            ];
         });

         $specializationCategory = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
            return $specialization->category_item->category_name;
         });
      }
      if ($actor->other_info != null) {
         $otherInfo = collect($actor->other_infos)->map(function ($otherInfo) use ($lang) {
            // dd($otherInfo['type'][0]->information_type_name);
            return [
               'index'  => $otherInfo['index'],
               'type'   => $otherInfo['type'],
               'value'  => $otherInfo['value'],
            ];
         });
      }

      $actor->fullname = $actor->full_name;
      $actor->specialization_item = $specialization;
      $actor->physical_code_item = $physicalCode;
      $actor->specialization_category = $specializationCategory;
      $actor->other_info_item = $otherInfo;
      $actor->actor_field_of_professions = $actor->actor_field_of_professions;
      $actor->actor_field_of_professions_items = $actor->actor_field_of_professions_items;
      $actor->actor_terms = $actor->actor_terms;
      // dd($actor);
      return response()->json($actor);
   }

   public function updateInformationList(Request $request)
   {
      $lang = $request->lang;
      
      $otherInfo = null;

      if ($request->other_info != null) $otherInfo = json_encode($request->other_info);

      $actor = Actor::findOrFail($request->id);
      $actor->other_info         = $otherInfo;
      $actor->update();

      $actor = Actor::with([
         'actors_brand_item',
         'actor_specialization.category_item',
         'actor_specialization.profession_item',
         'actor_specialization.specialization_item',
         'actor_field_of_professions',
         'actor_field_of_professions_items',
         'actor_terms',
      ])->findOrFail($request->id);

      $physicalCode = null;
      $specialization = null;
      $otherInfo = null;
      $specializationCategory = null;
      if ($actor->physical_code_types != null) {
         $physicalCode = collect($actor->physical_code_types)->map(function ($physicalCode) use ($lang) {
            return [
               'code'   => $physicalCode['code'],
               'index'  => $physicalCode['index'],
               'physical_code_type' => $physicalCode['physical_code_type'],
               'physical_code_and_description' => $physicalCode['physical_code_type']->map(function ($physicalCodeType) use ($lang, $physicalCode) {
                  // dd($physicalCodeType->name);
                  $name = $physicalCodeType->physical_code_type_name;
                  $code = $physicalCode['code'];
                  $physicalCodeType->typeAndCode = "{$name}: {$code}";
                  return $physicalCodeType->typeAndCode;
               })
            ];
         });
      }
      if (count($actor->actor_specialization)) {
         $specialization = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
            // dd($specialization);
            $specialization->specialization_item->profession_description = "{$specialization->profession_item->profession_name}: {$specialization->specialization_item->description_name}";
            $specialization->specialization_item->job_category_id = $specialization->category_item->id;
            return [
               'index'  => $key,
               'profession' => $specialization->specialization_item,
               'specialization' => '',
            ];
         });

         $specializationCategory = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
            return $specialization->category_item->category_name;
         });
      }
      if ($actor->other_info != null) {
         $otherInfo = collect($actor->other_infos)->map(function ($otherInfo) use ($lang) {
            // dd($otherInfo['type'][0]->information_type_name);
            return [
               'index'  => $otherInfo['index'],
               'type'   => $otherInfo['type'],
               'value'  => $otherInfo['value'],
            ];
         });
      }

      $actor->fullname = $actor->full_name;
      $actor->specialization_item = $specialization;
      $actor->physical_code_item = $physicalCode;
      $actor->specialization_category = $specializationCategory;
      $actor->other_info_item = $otherInfo;
      $actor->actor_field_of_professions = $actor->actor_field_of_professions;
      $actor->actor_field_of_professions_items = $actor->actor_field_of_professions_items;
      $actor->actor_terms = $actor->actor_terms;
      // dd($actor);
      return response()->json($actor);
   }

   public function linkTerm(Request $request) 
   {
      $childId = (int) $request->child_id;
      $parentId = (int) $request->id;

      $actor = Actor::findOrFail($parentId);
      
      if ($request->key == 'unlink') {
         $actor = ActorTerm::where('actor',$parentId)->where('term', $childId)->first();
         $actor->delete();
         return $actor;
      }

      $actor->actor_terms()->create([
         "term"  => $childId,
      ]);

      return $actor;
   }

   public function destroy(Request $request)
   {
      $actor = Actor::find($request->id);

      // if(!$actor->provider_actors()->exists()) {
      //    if ( $actor -> delete() ) {

      //       return response()->json(true);

      //    }
      // }

      if ($actor->delete()) {

         return response()->json(true);
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
      
      $actor = Actor::findOrFail($request->id);
      $actor->actors_brand_item()->delete();

      foreach (json_decode($request->brands) as $value) {
         $actor->actors_brand_item()->create([
            "brands"  => $value,
         ]);
      };

      $actor = Actor::with([
         'actors_brand_item',
         'actor_specialization.category_item',
         'actor_specialization.profession_item',
         'actor_specialization.specialization_item',
      ])->findOrFail($request->id);
      $physicalCode = null;
      $specialization = null;
      $otherInfo = null;
      $specializationCategory = null;
      if ($actor->physical_code_types != null) {
         $physicalCode = collect($actor->physical_code_types)->map(function ($physicalCode) use ($lang) {
            return [
               'code'   => $physicalCode['code'],
               'index'  => $physicalCode['index'],
               'physical_code_type' => $physicalCode['physical_code_type']->map(function ($physicalCodeType) use ($lang) {

                  $physicalCodeType->name = getTranslation($physicalCodeType->name, $lang);

                  return $physicalCodeType;
               })
            ];
         });
      }
      if (count($actor->actor_specialization)) {
         $specialization = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
            // dd($specialization);
            $specialization->specialization_item->profession_description = "{$specialization->profession_item->profession_name}: {$specialization->specialization_item->description_name}";
            $specialization->specialization_item->job_category_id = $specialization->category_item->id;
            return [
               'index'  => $key,
               'profession' => $specialization->specialization_item,
               'specialization' => '',
            ];
         });

         $specializationCategory = $actor->actor_specialization->map(function ($specialization, $key) use ($lang) {
            return $specialization->category_item->category_name;
         });
      }
      if ($actor->other_info != null) {
         $otherInfo = collect($actor->other_infos)->map(function ($otherInfo) use ($lang) {
            return [
               'index'  => $otherInfo['index'],
               'type'   => $otherInfo['type']->map(function ($otherInfo) use ($lang) {

                  $otherInfo->name = getTranslation($otherInfo->name, $lang);

                  return $otherInfo;
               }),
               'value'  => $otherInfo['value'],
            ];
         });
      }
      $actor->fullname = $actor->full_name;
      $actor->specialization_item = $specialization;
      $actor->physical_code_item = $physicalCode;
      $actor->specialization_category = $specializationCategory;
      $actor->other_info_item = $otherInfo;
      return response()->json($actor);
   }

   public function paginate($items, $perPage = 10, $page = 1)
   {

      $items = $items instanceof Collection ? $items : Collection::make($items);
      $currentPageResults = $items->slice(($page - 1) * $perPage, $perPage)->values();
      return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage);
   }
}
