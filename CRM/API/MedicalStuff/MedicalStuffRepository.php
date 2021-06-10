<?php

namespace CRM\API\MedicalStuff;

use App\Models\MedicalStuff\MedicalTerm;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\SyncReference;
use App\Repositories\BaseRepository;
use CRM\API\MedicalStuff\Traits\ItemsTrait;
use CRM\API\MedicalStuff\Traits\MedicalStuffTrait;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicalStuffRepository extends BaseRepository
{

   use MedicalStuffTrait, ItemsTrait;

   public function __construct(MedicalTerm $model)
   {
      $this->model = $model;
   }

   public function model()
   {
      return $this->model;
   }

   public function get_active_terms()
   {
      return $this->model
         ->select('medical_terms.*')
         ->active()
         ->orderBy('name')
         ->get();
   }

   public function total_active_terms(): int
   {
      return $this->model
         ->select('medical_terms.*')
         ->active()
         ->get()
         ->count();
   }

   public function getActivePaginated($paged,  $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
   {
      $request = request();

      $paginate = $this->model
         ->select(
            'medical_terms.*',
            DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(term_types, '[', -1), ']', 1) as linked_term_type_id"),
            DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(specializations, '[', -1), ']', 1) as linked_specialization_id"),
            DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(medical_terms, '[', -1), ']', 1) as linked_term_id")
         )
         ->active()
         ->when($request->parent_id, function ($query) use ($request) {
            $query
               ->where('medical_terms.id', '!=', $request->parent_id)
               ->sortedByLinkedTerm();
         })
         ->when($request->sortbyTerm, function ($query) use ($request) {
            $query->sortedByTerm($request->sortbyTerm);
         })
         ->when($request->sortbyLang, function ($query) use ($request) {
            $query->sortedByNoLang($request->sortbyLang);
         })
         ->when($request->sortByArticle, function ($query) use ($request) {
            $query->sortedByArticleTerm($request->article_id);
         })
         ->when($request->filter, function ($query) {
            $query->filtered();
         })
         ->when($request->to_advance_search, function ($query) use ($request) {
            $query
               ->when($request->specializations, function ($subQuery) {
                  $subQuery->checkBySpecilization();
               })
               ->when($request->terms, function ($subQuery) {
                  $subQuery->checkByTerm();
               })
               ->when($request->type_terms, function ($subQuery) {
                  $subQuery->checkByTypeTerm();
               })
               ->when($request->articles, function ($subQuery) {
                  $subQuery->checkByArticle();
               });
         })
         ->with([
            'medical_articles', 'brands', 'service', 'user_created'
         ])
         ->orderBy($orderBy,  $sort)
         ->paginate($paged);

      return  $paginate;
   }

   public function getAll($request)
   {
      $data = $this->model
         ->select('medical_terms.*')
         ->active()
         ->when($request->type, function ($query) use ($request) {
            $query
               ->where('medical_terms.id', '!=', $request->parent_id);
         })
         ->when(!$request->reports, function ($query) {
            $query
               ->with(['medical_articles', 'brands', 'service']);
         })
         ->get();

      if (strlen($request->filter) > 0) {
         $data = collect($data)
            ->filter(function ($item) use ($request) {
               return false !== stristr($item->term_name, $request->filter);
            })
            ->values()
            ->all();
      }

      return $data;
   }


   public function findById($id)
   {
      return $this
         ->with([
            'medical_articles', 'brands'
         ])
         ->getById($id);
   }

   public function get_active_term($id)
   {
      return $this
         ->model
         ->select('medical_terms.*')
         ->active()
         ->with([
            'medical_articles', 'brands'
         ])
         ->find($id);
   }

   public function findByIds($ids)
   {
      return MedicalTerm::with(['medical_articles', 'brands'])->findMany($ids);
   }

   public function create(array $data)
   {

      return DB::transaction(function () use ($data) {

         $termTypesArray = json_decode($data['term_types']) ?? [];
         $locale = request()->locale;
         $user = request()->user();

         $specializations = $data['specializations'] ? $this->extract_array_to_json(json_decode($data['specializations']), 'specializations') : null;
         $termtypes = $data['term_types'] ? $this->extract_array_to_json($termTypesArray, 'term_types') : null;

         $term = $this->model->create([
            'name' => to_json_add($locale, request()->name),
            'specializations' => $specializations,
            'term_types' => $termtypes,
            'created_by' => $user->isActive() ? $user->id : null
         ]);

         if ($term && request()->hasFile('file')) {
            $this->upload_image($term, request()->file('file'));
         }

         if ($term && request()->hasFile('icon')) {
            $this->upload_icon($term, request()->file('icon'));
         }

         // temporary remove
         // if (!empty($termTypesArray) && $data = $this->setServiceDetails($term, $termTypesArray)) {
         //    $this->store_service($data);
         // }

         return $term;
      });
   }

   public function update_geolocalization_sync_reference_for_link_term($articleID, $termID)
   {
      $article = MedicalArticle::with(['geolocalizations', 'geolocalization_sync_reference'])->findOrFail($articleID);
      $geolocalizations = $article->geolocalizations;
      if (!$geolocalizations->isEmpty()) {
         foreach ($geolocalizations as $geolocalization) {

            $medicalArticle = DB::table('medical_term_article_link as mtl')
               ->leftJoin('provider_services as ps', 'ps.services', 'mtl.medical_term_id')
               ->leftJoin('providers as p', 'p.id', 'ps.providers')
               ->select(['p.id as providers_id', 'p.name as providers_name'])
               ->where('mtl.medical_article_id', '=', $geolocalization->article)
               ->where('p.country', '=', $geolocalization->country)
               ->where('p.division', '=', $geolocalization->division);

            $provider_list = [];

            foreach ($medicalArticle->get() as $prov) {

               $services = DB::table('provider_services')->where('providers', '=', $prov->providers_id)->get();

               $services_list = [];
               foreach ($services as $serv) {
                  $medical_link = DB::table('medical_term_article_link as mtal')
                     ->leftJoin('medical_terms as mt', 'mt.id', 'mtal.medical_term_id')
                     ->where('mtal.medical_article_id', '=', $geolocalization->article)
                     ->where('mt.id', '=', $serv->services)
                     ->select(['mt.id', 'mt.name'])
                     ->get();
                  foreach ($medical_link as $medical_link_list) {
                     if ($termID == $medical_link_list->id) {
                        $this->update_geolocalization_sync_reference($article);
                        break;
                     }
                  }
               }
            }
         }
      }
   }

   public function update($id, array $data)
   {
      return DB::transaction(function () use ($id, $data) {
         $locale = request()->locale;
         $termTypesArray = json_decode($data['term_types']) ?? [];

         $specializations = $data['specializations'] ? $this->extract_array_to_json(json_decode($data['specializations']), 'specializations') : null;
         $termtypes = $data['term_types'] ? $this->extract_array_to_json($termTypesArray, 'term_types') : null;

         $term = $this->getById($id);
         $term->name = to_json_add($locale, $data['name'], to_json_remove($locale, $term->name));
         $term->specializations = $specializations;
         $term->term_types = $termtypes;
         $term->save();

         if ($term && request()->hasFile('file')) {
            $this->upload_image($term, request()->file('file'));
         }

         if ($term && request()->hasFile('icon')) {
            $this->upload_icon($term, request()->file('icon'));
         }

         $this->update_provider_sync_reference($term);

         return $term;
      });
   }

   public function setServiceDetails($term, $Types)
   {
      $data = collect($Types)
         ->map(function ($type) use ($term) {
            if ($type->is_service) {
               return (object)[
                  'term_id' => $term->id,
                  'term_name' => $term->base_name,
                  'term_img_url' => $term->img_url
               ];
            }
         })
         ->first();

      return $data;
   }

   // Linked Terms Query
   public function linked_terms($request)
   {
      $idArrray  = json_decode($request->value) ?? [];
      $parentId = $request->parent_id ?: false;
      $items = [];

      $termItems = collect($idArrray)
         ->map(function ($id) {
            return $this->getById($id);
         });

      foreach ($termItems as $term) {
         if ($termTypes = $term->has_term_types) {
            $count = 0;
            foreach ($termTypes as $termType) {
               $descriptions = $parentId ? $this->linked_term_description((int)$parentId, (int)$term->id) : [];

               $items[$termType->term_type_name][] = [
                  'id' => $term->id,
                  'name' => $term->term_name,
                  'id_slug_name' => str_slug("{$term->base_name} {$count}"),
                  'descriptions' => $descriptions,
                  'route_show' => $term->route_show,
               ];
               $count++;
            }
         }
      }
      return $items;
   }

   public function linked_provider_type_terms($request, $providerTypeId)
   {
      $items = [];
      $idArrray = json_decode($request->value) ?? [];
      $termItems = [];

      foreach ($idArrray as $id) {
         $termItems[] = $this->getById($id);
      }
      $count = 0;
      foreach ($termItems as $value) {
         $termTypes = $value->has_term_types;
         if ($termTypes) {
            $type = '';
            foreach ($termTypes as $termType) {
               if ($type !== $termType->term_type_name) {
                  $name = preg_replace('/<.+$/', '', $termType->term_type_name);
                  $items[$name][] = [
                     'id' => $value->id,
                     'name' => $value->term_name,
                     'id_slug_name' => str_slug($value->base_name . '-' . $count),
                     'descriptions' => $this->linked_term_description((int)$request->parent_id, (int)$value->id),
                     'route_show' => str_contains($value->medical_terms, $providerTypeId) ? $value->route_show : 'disabled',
                  ];
               } else {
                  $items[$type][] = [
                     'id' => $value->id,
                     'name' => $value->term_name,
                     'id_slug_name' => str_slug($value->base_name . '-' . $count),
                     'descriptions' => $this->linked_term_description((int)$request->parent_id, (int)$value->id),
                     'route_show' => str_contains($value->medical_terms, $providerTypeId) ? $value->route_show : 'disabled',
                  ];
               }

               $count += 1;
            }
         }
      }
      return $items;
   }

   public function linked_terms_actor($request)
   {
      // $idArrray  = json_decode($request->value) ?? [];
      $items     = [];

      $termItems = collect($request->value)
         ->map(function ($value) {
            return $this->getById(json_decode($value)->term);
         });

      foreach ($termItems as $term) {
         if ($termTypes = $term->has_term_types) {
            $count = 0;
            foreach ($termTypes as $termType) {
               $items[$termType->term_type_name][] = [
                  'id' => $term->id,
                  'name' => $term->term_name,
                  'id_slug_name' => str_slug("{$term->base_name} {$count}"),
                  'route_show' => $term->route_show,
               ];
               $count++;
            }
         }
      }

      return $items;
   }

   public function group_terms($request)
   {
      $items = [];
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
      $idArrray = json_decode($request->value) ?? [];
      $termItems = [];

      foreach ($idArrray as $id) {
         $termItems[] = $this->getById($id);
      }
      $filteredTerms = collect($termItems)->groupBy(function ($term) use ($request, $categoryServiceNames) {
         // if($term->name == '{"it":"Mammografia"}') dd($term->has_term_types);
         if (collect($term->has_term_types)->contains(function ($value) use ($request, $categoryServiceNames) {
            $language = $this->get_available_language($value->name, $request->locale);
            $name = json_decode($value->name)->$language;
            return in_array($name, $categoryServiceNames, true);
         })) {
            return getTranslation($term->name, $request->locale);
         } else {
            return 'No Category Of Service';
         }
      });

      return $filteredTerms;
   }

   // public function linked_provider_terms($request)
   // {
   //    // $this
   //    // $items = [];
   //    // $providerTypeNames = [
   //    //    'en' => [
   //    //       'Provider',
   //    //       'Provider Type',
   //    //    ],
   //    // ];
   //    // $idArrray = json_decode($request->term_ids) ?? [];
   //    // $termItems = [];
   //    // foreach ($idArrray as $id) {
   //    //    $termItems[] = $this->getById($id);
   //    // }
   //    // $termItems = collect($termItems)->map(function ($term) use ($request, $providerTypeNames) {
   //    //    if (collect($term->has_term_types)->contains(function ($value, $key) use ($request, $providerTypeNames) {
   //    //       $name = getTranslation($value->name, $request->locale);
   //    //       return in_array($name, $providerTypeNames['en'], true);
   //    //    })) {
   //    //       return $term;
   //    //    }
   //    // })->filter(function ($value, $key) {
   //    //    return $value != null;
   //    // })->values();

   //    return $termItems;
   // }

   public function linked_terms_details($request)
   {
      $items = [];
      $idArrray = $request->term_ids ?? [];
      $termItems = [];

      foreach ($idArrray as $id) {
         $termItems[] = $this->getById($id);
      }

      $count = 0;
      // term_type_id
      foreach ($termItems as $value) {
         $termTypes = collect($value->has_term_types)->where('id', $request->term_type_id);
         if ($termTypes) {
            $type = '';
            foreach ($termTypes as $termType) {
               if ($type !== $termType->term_type_name) {
                  $items[] = [
                     'id' => $value->id,
                     'name' => $value->term_name,
                     'id_slug_name' => str_slug($value->base_name . '-' . $count),
                     'descriptions' => $this->linked_term_description((int)$request->parent_id, (int)$value->id),
                     'route_show' => $value->route_show,
                  ];
               } else {
                  $items[] = [
                     'id' => $value->id,
                     'name' => $value->term_name,
                     'id_slug_name' => str_slug($value->base_name . '-' . $count),
                     'descriptions' => $this->linked_term_description((int)$request->parent_id, (int)$value->id),
                     'route_show' => $value->route_show,
                  ];
               }

               $count += 1;
            }
         }
      }
      // 
      return $items;
   }

   // Post Term link link
   public function link_term($request) // Post link
   {
      $childId = (int) $request->child_id;
      $parentId = (int) $request->id;
      $term = $this->getById($parentId);
      $value = $this->set_term_valueId(json_decode($term->medical_terms) ?? [], $childId, $request->key);

      if ($request->key == 'unlink') {
         $this->remove_term_description($parentId, $childId);
      }

      $term->medical_terms = $value;
      $term->save();

      // link the child terms
      $this->link_child_term($request);

      return  $term;
   }

   public function link_child_term($request): void
   {
      $term = $this->getById($request->child_id);
      $value = $this->set_term_valueId(json_decode($term->medical_terms) ?? [], $request->id, $request->key);
      $term->medical_terms =  $value;
      $term->save();
   }

   public function remove($request)
   {
      $term = $this->getById($request->id);

      if (!empty($linkedId = json_decode($term->medical_terms) ?? [])) {
         $this->remove_medical_term($linkedId, $term->id);
      }

      if ($term) {
         $this->upload_image($term, null);

         // $this->remove_service($term->id); // remove term service
      }

      return $term->delete();
   }

   public function extract_array_to_json($data, $type)
   {
      $items = [];

      switch ($type) {
         case 'specializations':
            $array = [];
            foreach ($data as $value) {
               $array[] = $value->id;
            }
            $items[$type] =  $array;

            break;
         case 'term_types':
            $array = [];
            foreach ($data as $value) {
               $array[] = $value->id;
            }

            $items[$type] =  $array;

            break;

         case 'parent_id':
            $array = [];
            foreach ($data as $id) {
               $array[] = $id;
            }
            $items[$type] =  $array;

            break;
      }
      return json_encode($items);
   }

   public function setSortByTerm($data, $sortedValue)
   {
      if ($sortedValue === 'all') {
         return $data;
      }

      $arr = explode('_', $sortedValue);

      $sortBy = $arr[0] === 'most' ? 'sortByDesc' : 'sortBy';

      $terms = $data
         ->$sortBy(function ($term) use ($sortedValue) {
            switch ($sortedValue) {
               case 'least_lt':
                  if ($term->has_terms_id) return count($term->has_terms_id);
                  return 0;
               case 'most_lt':
                  if ($term->has_terms_id) return count($term->has_terms_id);
                  return 0;
               case 'least_la':
                  return count($term->medical_articles);
               case 'most_la':
                  return count($term->medical_articles);
               case 'least_tt':
                  if ($term->has_term_types) return count($term->has_term_types);
                  return 0;
               case 'most_tt':
                  if ($term->has_term_types) return count($term->has_term_types);
                  return 0;
                  if ($term->has_specializations) return count($term->has_specializations);
                  return 0;
               case 'most_spec':
                  if ($term->has_specializations) return count($term->has_specializations);
                  return 0;
            }
         })
         ->values();

      return $terms;
   }

   public function setSortByLang($data, $sortedValue)
   {
      if ($sortedValue === 'all') {
         return $data;
      }

      $terms = $data
         ->filter(function ($item) use ($sortedValue) {
            switch ($sortedValue) {
               case 'no-tt':
                  if (!$item->has_term_types) return $item;
                  break;
               case 'no-spec':
                  if (!$item->has_specializations) return $item;
                  break;
               case 'no-la':
                  if ($item->medical_articles->count() === 0) return $item;
                  break;
               case 'no-lt':
                  if (!$item->has_terms_id) return $item;
                  break;
               default:
                  if (!$item->has_translation) return $item;
                  break;
            }
         })
         ->values();

      return $terms;
   }

   public function setGroupByTermType($request)
   {
      $providerTypes = [];
      foreach ($request->provider_types as $provider_type) {
         $data = json_decode($provider_type);

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

         $serviceNames = [
            'Service',
            'Servizio',
            'Servizi',
            'Serbisyo',
         ];

         $providerTypeNames = [
            'en' => [
               'Provider',
               'Provider Type',
            ],
         ];
         $isDisabledServices = false;
         $categories = array();
         $uncheckedServices = array();
         $request['value'] = $data->medical_terms;
         $request['parent_id'] = $data->id;

         $data->medical_terms_value = $this->group_terms($request);
         if ($request->term_name) {
            $termSearch = $request->term_search;
            $terms = $data->filter(function ($term) use ($termSearch) {
               return false !== stristr($term->name, $termSearch);
            })->values();

            $terms = $terms->map(function ($term, $key) use ($request) {
               $term->name = getTranslation($term->name, $request->locale);
               return $term;
            });
            return $terms;
         }

         $withCategoryIds = array();
         $withoutCategory = array();

         foreach ($data->medical_terms_value as $key => $terms) {
            if ($key != 'No Category Of Service') array_push($categories, $key);

            foreach ($terms as $key => $term) {
               if ($term->medical_terms) {
                  if (str_contains($term->medical_terms, $data->id)) {
                     $request['value'] = $term->medical_terms;
                     $request['parent_id'] = $term->id;
                     $term->medical_terms_value = $this->linked_provider_type_terms($request, $data->id);
                     foreach ($term->medical_terms_value as $key => $value) {
                        if (in_array($key, $serviceNames, true)) {
                           foreach ($value as $key => $service) {
                              array_push($withCategoryIds, $service['id']);
                           }
                        }
                     }
                  }
               }
            }
         }

         $filteredTerms = $data->medical_terms_value->map(function ($term, $key) use ($request, $categoryServiceNames, $serviceNames, $providerTypeNames, $categories, $data, $withCategoryIds) {
            if ($key == 'No Category Of Service') {
               return $term->map(function ($medicalTerm) use ($request, $categoryServiceNames, $serviceNames, $providerTypeNames, $categories, $data, $withCategoryIds) {
                  if (in_array($medicalTerm->id, $withCategoryIds, true)) return;
                  if (!$medicalTerm->medical_terms) return;
                  if (!str_contains($medicalTerm->medical_terms, $data->id)) return;
                  $request['value'] = $medicalTerm->medical_terms;
                  $request['parent_id'] = $medicalTerm->id;
                  $medicalTerm->medical_terms_value = $this->linked_provider_type_terms($request, $data->id);
                  if (!collect($medicalTerm->medical_terms_value)->contains(function ($value, $key) use ($request, $providerTypeNames) {
                     $name = preg_replace('/<.+$/', '', $key);
                     return in_array($name, $providerTypeNames['en'], true);
                  })) {
                     return;
                  }

                  if (collect($medicalTerm->medical_terms_value)->contains(function ($value, $key) use ($request, $providerTypeNames, $categories) {
                     foreach ($value as $name) {
                        if (in_array(preg_replace('/<.+$/', '', $name['name']), $categories, true)) {
                           return true;
                        }
                     }
                  })) {
                     return;
                  }

                  if (!collect($medicalTerm->medical_terms_value)->contains(function ($value, $key) use ($request, $categoryServiceNames) {
                     $name = preg_replace('/<.+$/', '', $key);
                     return in_array($name, $categoryServiceNames, true);
                  })) {

                     return [
                        'id' => $medicalTerm->id,
                        'name' => getTranslation($medicalTerm->name, $request->locale),
                     ];
                  } else {
                     foreach ($categoryServiceNames as $value) {
                        if (isset($medicalTerm->medical_terms_value[$value])) {
                           if (!collect($medicalTerm->medical_terms_value[$value])->contains(function ($value, $key) use ($request, $categories) {
                              $name = $value['name'];
                              return in_array($name, $categories, true);
                           })) {
                              return [
                                 'id' => $medicalTerm->id,
                                 'name' => getTranslation($medicalTerm->name, $request->locale),
                                 'route_show' => 'disabled',
                              ];
                           }
                        }
                     }
                  }
               })->filter(function ($value, $key) {
                  return $value != null;
               })->values();
            } else {
               if (!$term[0]->medical_terms) return;

               if (!str_contains($term[0]->medical_terms, $data->id)) return;
               $request['value'] = $term[0]->medical_terms;
               $request['parent_id'] = $term[0]->id;
               $term[0]->medical_terms_value = $this->linked_provider_type_terms($request, $data->id);
               foreach ($term[0]->medical_terms_value as $key => $value) {
                  if (in_array($key, $serviceNames, true)) {
                     return $value;
                  }
               }
            }
         })->filter(function ($value, $key) {
            return $value != null;
         })->filter(function ($value, $key) {
            return count($value) > 0;
         })->toArray();

         foreach ($filteredTerms as $key => $value) {
            if ($key != 'No Category Of Service') {
               foreach ($value as $service) {
                  if (isset($service['route_show'])) {
                     if ($service['route_show'] == 'disabled') {
                        $isDisabledServices = true;
                        break;
                     }
                  }
               }
            }
         }

         if (isset($filteredTerms['No Category Of Service'])) {
            foreach ($filteredTerms['No Category Of Service'] as $service) {
               if (isset($service['route_show'])) {
                  if ($service['route_show'] == 'disabled') {
                     array_push($uncheckedServices, $service);
                  }
               }
            }
         }

         if (isset($filteredTerms['No Category Of Service'])) {
            $item = $filteredTerms['No Category Of Service'];
            unset($filteredTerms['No Category Of Service']);
            $filteredTerms['No Category Of Service'] = collect($item)->map(function ($term) {
               if (!isset($term['route_show'])) {
                  return $term;
               }
            })->filter(function ($value, $key) {
               return $value != null;
            })->values();

            if (count($filteredTerms['No Category Of Service']) == 0) {
               unset($filteredTerms['No Category Of Service']);
            };
         }

         $services = [
            'checkableServices' => $filteredTerms,
            'uncheckableServices' => $uncheckedServices,
            'isDisabledServices' => $isDisabledServices,
         ];
         $providerTypes[$data->base_name] = $services;
      }

      return $providerTypes;
   }

   public function setSortByGeolocalization($data, $sortedValue)
   {
      if ($sortedValue === 'all') {
         return $data;
      }

      $terms = $data
         ->filter(function ($item) use ($sortedValue) {
            // dd($item->geolocalization_template);
            switch ($sortedValue) {
               case 'no-gc':
                  if (!$item->geolocalizations->contains('area', 'City')) return $item;
                  break;
               case 'no-gp':
                  if (!$item->geolocalizations->contains('area', 'Province')) return $item;
                  break;
               case 'no-gr':
                  if (!$item->geolocalizations->contains('area', 'Region')) return $item;
                  break;
               case 'no-t':
                  if (!$item->geolocalization_template) return $item;
                  break;
               case 'wuga':
                  if ($item->geolocalizations->contains('publish_status', 'Unpublished')) return $item;
                  break;
               case 'wpga':
                  if ($item->geolocalizations->contains('publish_status', 'Published')) return $item;
                  break;
               default:
                  if (!$item->has_translation) return $item;
                  break;
            }
         })
         ->values();

      return $terms;
   }

   public function getProviderTypeOrService($data, $request)
   {
      $providerTypeNames = [
         'en' => [
            'Provider',
            'Provider Type',
         ],
      ];

      $serviceNames = [
         'Service',
         'Servizio',
         'Servizi',
         'Serbisyo',
      ];

      if ($request->filterByProviderType) {
         $filteredTerms = $data->filter(function ($term) use ($request, $providerTypeNames) {
            return collect($term->has_term_types)->filter(function ($term) use ($request, $providerTypeNames) {
               $language = $this->get_available_language($term->name, $request->locale);
               $name = json_decode($term->name)->$language;
               return in_array($name, $providerTypeNames['en'], true);
            })->count() > 0;
         })->values();
      }

      if ($request->filterByService) {
         $filteredTerms = $data->filter(function ($term) use ($request, $serviceNames) {
            return collect($term->has_term_types)->filter(function ($term) use ($request, $serviceNames) {
               $language = $this->get_available_language($term->name, $request->locale);
               $name = json_decode($term->name)->$language;
               return in_array($name, $serviceNames, true);
            })->count() > 0;
         })->values();

         $filteredTerms = $filteredTerms->map(function ($term, $key) use ($request, $serviceNames, $providerTypeNames) {
            if (!$term->medical_terms) return;
            if (!str_contains($term->medical_terms, $request->provider_type)) return;
            $request['value'] = $term->medical_terms;
            $request['parent_id'] = $term->id;
            $term->medical_terms_value = $this->linked_terms($request);
            if (!collect($term->medical_terms_value)->contains(function ($value, $key) use ($request, $providerTypeNames) {
               $name = preg_replace('/<.+$/', '', $key);
               return in_array($name, $providerTypeNames['en'], true);
            })) {
               return;
            }

            return [
               'id' => $term->id,
               'name' => getTranslation($term->name, $request->locale),
            ];
         })->filter(function ($value, $key) {
            return $value != null;
         })->values();

         return $filteredTerms;
      }


      $terms = $filteredTerms->map(function ($term) use ($request) {
         return [
            'id' => $term->id,
            'name' => getTranslation($term->name, $request->locale),
         ];
      });

      return $terms;
   }

   public function getService($data, $request)
   {
      $serviceNames = [
         'en' => [
            'Service',
         ],

         'it' => [
            'Servizio',
            'Servizi',
         ]
      ];

      $filteredTerms = $data->filter(function ($term) use ($request, $serviceNames) {
         return collect($term->has_term_types)->filter(function ($term) use ($request, $serviceNames) {
            $language = $this->get_available_language($term->name, $request->locale);
            $name = json_decode($term->name)->$language;
            return in_array($name, $serviceNames['en'], true);
         })->count() > 0;
      })->values();

      $terms = $filteredTerms->map(function ($term) use ($request) {
         return [
            'id' => $term->id,
            'name' => getTranslation($term->name, $request->locale),
         ];
      });

      return $terms;
   }
}
