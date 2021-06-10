<?php

namespace App\Models\Provider;

use Artisan;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\MedicalStuff\MedicalTerm;
use CRM\API\MedicalStuff\Traits\MedicalStuffTrait;
use App\Models\Provider;
use App\Models\ProviderService;


class ProviderAndItsType extends Model
{
    
    // use Searchable;

    use MedicalStuffTrait;

    protected $table = 'provider_and_its_type';
    protected $guarded = [];

    // protected $appends = ['selected_brands', 'provider_name'];

    // public function searchableAs()
    // {   
    //     if(request()->getHttpHost() == 'stagingcct.med4care.online') {
    //         return config('scout.prefix').'provider_staging';
    //     }
    //     return config('scout.prefix').'providers';
    // }

    // public function toSearchableArray()
    // {   
    //     if($this->provider_item == null) return;
    //     $objectID = 'App\Models\Provider\ProviderAndItsType::'.$this->id;
    //     $query = null;
    //     $postID = 0;
    //     $provider = $this->provider_item;
    //     $providerType = $this->type_of_provider_item;
    //     $categoryServiceNames = [
    //         'Category of Service',
    //         'Category of Services',
    //         'Categories of Service',
    //         'Service Categories',
    //         'Service Category',
    //         'Categoria di Servizio',
    //         'Categoria di Servizi',
    //         'Categorie di Servizio',
    //     ];

    //     $serviceNames = [
    //         'Service',
    //         'Servizio',
    //         'Servizi',
    //         'Serbisyo',
    //     ];

    //     $providerTypeNames = [
    //         'en' => [
    //             'Provider',
    //             'Provider Type',
    //         ],
    //     ];
    //     $provider_services = $provider->provider_services;
    //     $services = null;
    //     $categoryOfServiceArray = array();
    //     $categories = array();
    //     $servicesExclusive = null;
        
    //     if($provider->provider_sync_reference->action != 'New') {
            
    //         $providerAndItsTypes = ProviderAndItsType::search($query, function ($algolia, $query, $options) use($objectID){
    //             $new_options = [];
    //             $new_options = ["facetFilters"=>"objectID:".$objectID];
    //             return $algolia->search($query, array_merge($options,$new_options));
    //         })->raw();

    //         if(isset($providerAndItsTypes['hits'][0]['post_id'])) {
    //             $postID = $providerAndItsTypes['hits'][0]['post_id'];
    //         };
    //     };

    //     if(!$provider_services->isEmpty()) {
    //         $providerServicesArr = $provider_services->pluck('services')->toArray();
    //         $request['value'] = $providerType->medical_terms;
    //         $request['parent_id'] = $providerType->id;

    //         $providerType->medical_terms_value = $this->group_terms($request);

    //         $withCategoryIds = array();
    //         foreach ($providerType->medical_terms_value['terms'] as $key => $terms) {
    //             if ($key != 'No Category Of Service') array_push($categories, $key);

    //             foreach ($terms as $key => $term) {
    //             if (!$term->medical_terms) return;
                
    //             if (!str_contains($term->medical_terms, $providerType->id)) return;
    //             $request['value'] = $term->medical_terms;
    //             $request['parent_id'] = $term->id;
    //             $term->medical_terms_value = $this->linked_provider_type_terms($request, $providerType->id, $provider->id, $providerServicesArr);
    //             foreach ($term->medical_terms_value as $key => $value) {
    //                 if (in_array($key, $serviceNames, true)) {
    //                     foreach ($value as $key => $service) {
    //                         array_push($withCategoryIds, $service['id']);
    //                     }
    //                 }
    //             }
    //             }
    //         }
    //         $services = $providerType->medical_terms_value['terms']->map(function ($term, $key) use ($request, $providerServicesArr, $categoryServiceNames, $serviceNames, $providerTypeNames, $categories, $providerType, $withCategoryIds, $provider) {
    //             if ($key == 'No Category Of Service') {
    //                return $term->map(function ($medicalTerm) use ($request, $providerServicesArr, $categoryServiceNames, $serviceNames, $providerTypeNames, $categories, $providerType, $withCategoryIds, $provider) {
    //                     if (!in_array($medicalTerm->id, $providerServicesArr ,true)) return;
    //                     if (in_array($medicalTerm->id, $withCategoryIds, true)) return;
    //                     if (!$medicalTerm->medical_terms) return;
    //                     if (!str_contains($medicalTerm->medical_terms, $providerType->id)) return;
    //                     $request['value'] = $medicalTerm->medical_terms;
    //                     $request['parent_id'] = $medicalTerm->id;
    //                     $medicalTerm->medical_terms_value = $this->linked_provider_type_terms($request, $providerType->id, $provider->id);
    //                     if (!collect($medicalTerm->medical_terms_value)->contains(function ($value, $key) use ($request, $providerTypeNames) {
    //                         $name = preg_replace('/<.+$/', '', $key);
    //                         return in_array($name, $providerTypeNames['en'], true);
    //                     })) {
    //                         return;
    //                     }
        
    //                     if (collect($medicalTerm->medical_terms_value)->contains(function ($value, $key) use ($request, $providerTypeNames, $categories) {
    //                         foreach ($value as $name) {
    //                             if (in_array(preg_replace('/<.+$/', '', $name['name']), $categories, true)) {
    //                             return true;
    //                             }
    //                         }
    //                     })) {
    //                         return;
    //                     }
    //                     if (!collect($medicalTerm->medical_terms_value)->contains(function ($value, $key) use ($request, $categoryServiceNames) {
    //                         $name = preg_replace('/<.+$/', '', $key);
    //                         return in_array($name, $categoryServiceNames, true);
    //                     })) {
    //                         $dateNow = date('Y-m-d');
    //                         $providerService = ProviderService::where('providers', $provider->id)->where('services', $medicalTerm->id)->first();
    //                         $notes = $providerService->service_item->note ? getTranslation($providerService->service_item->note,'en') : null;
    //                         if($providerService->day_end == '0000-00-00' || $dateNow < $providerService->day_end) { 
    //                             return [
    //                                 'id' => $medicalTerm->id,
    //                                 'name' => getTranslation($medicalTerm->name, locale()),
    //                                 'service_notes' => $notes,
    //                                 'parameter'     => $providerService->parameter_item == null ? null : $providerService->parameter_item->name,
    //                                 'images'        => $providerService->service_item->images,
    //                                 'description'   => $providerService->description,
    //                                 'services_icons'         => $providerService->service_item->term_icons->map(function ($termIcon) {
    //                                     return [
    //                                         'icon'              => $termIcon->icon,
    //                                         'provider_type'     => $termIcon->medical_term_provider_type->base_name,
    //                                     ];
    //                                 })
    //                             ];
    //                         }
    //                         return;
    //                     }
    //                })->filter(function ($value, $key) {
    //                   return $value != null;
    //                })->values();
    //             } else {
    //                 if (!$term[0]->medical_terms) return;
                
    //                 if (!str_contains($term[0]->medical_terms, $providerType->id)) return;
    //                 $request['value'] = $term[0]->medical_terms;
    //                 $request['parent_id'] = $term[0]->id;
    //                 $term[0]->medical_terms_value = $this->linked_provider_type_terms($request, $providerType->id, $provider->id, $providerServicesArr);
    //                 foreach ($term[0]->medical_terms_value as $key => $value) {
    //                     if (in_array($key, $serviceNames, true)) {
    //                         return $value;
    //                     }
    //                 }
    //             }
    //         })->filter(function ($value, $key) {
    //             return $value != null;
    //         })->filter(function ($value, $key) {
    //             return count($value) > 0;
    //         })->map(function ($term, $categoryOfService) use(&$categoryOfServiceArray, $providerType) {
    //             return [
    //                 'category_of_service'       => $categoryOfService,
    //                 'category_notes'            => $providerType->medical_terms_value['termsArray'][$categoryOfService]['notes'], 
    //                 'category_icons'            => $providerType->medical_terms_value['termsArray'][$categoryOfService]['icons']->map(function ($termIcon) {
    //                     return [
    //                         'icon'              => $termIcon->icon,
    //                         'provider_type'     => $termIcon->medical_term_provider_type->base_name,
    //                     ];
    //                 }),
    //                 'services' => $term,
    //             ];
    //         })->filter(function ($value, $key) {
    //             return $value != null;
    //         })->values();
            
    //     }
    //     $content = [
    //         'name'                  => getTranslation($provider->name,'en'),
    //         'country'               => $provider->country == null ? null : ['name'  => $provider->country_item->name],
    //         'city'                  => $provider->city == null ? null : ['name'  => $provider->city_item->name],
    //         'division'              => $provider->division == null ? null : ['name'  => $provider->division_item->name],
    //         'address'               => $provider->address,
    //         'contact_no'            => $provider->contact_no,
    //         'website'               => $provider->website,
    //         'images'                => $provider->images,
    //         'facebook_url'          => $provider->facebook_url,
    //         'account_status'        => $provider->account_status,
    //         'all_services'          => $services,
    //         'brand'                 => $provider->providers_brand_item == null ? null : $provider->providers_brand_item->brand_object->name,
    //     ];
            
    //     $data = [
    //         'record_status' => $provider->provider_sync_reference->action == 'Update' ? 'recently updated' : 'added',
    //         'title' => getTranslation($provider->name,'en'),
    //         'provider_type' => json_encode(['id'=> $providerType->id, 'name'=> getTranslation($providerType->name, 'en')]),
    //         'author' => null,
    //         'excerpt' => '',
    //         'content' => json_encode($content),
    //         'category' => getTranslation($providerType->name, 'en'),
    //         'tags' => [],
    //         'url' => null,
    //         'custom_field' => false,
    //         'post_status' => 'publish',
    //         'post_type' => 'providers',
    //         'post_id' => $postID,
    //     ];
        
    //     return $data;
    // }

    public function type_of_provider_item() {
        return $this->belongsTo(MedicalTerm::class, 'type_of_provider');
    }

    public function provider_item() {

        return $this->belongsTo(Provider::class, 'provider');

    }

    public function linked_terms($request, $providerTypeId)
    {   
        $idArrray  = json_decode($request['value']) ?? [];
        $items     = [];
        $termItems = [];
        $termItems = collect($idArrray)->map(function ($id) {
            return  MedicalTerm::find($id);
        });
        foreach ($termItems as $term) {
            if(in_array($providerTypeId, json_decode($term->medical_terms))) {
                if ($termTypes = $term->has_term_types) {
                    $count = 0;
                    foreach ($termTypes as $termType) {
                    $items[preg_replace('/<.+$/', '',$termType->term_type_name)][] = [
                        'id' => $term->id,
                        'name' => $term->term_name,
                        'id_slug_name' => str_slug("{$term->base_name} {$count}"),
                        'notes' => $term->note,
                        'route_show' => $term->route_show,
                        'icons' => $term->term_icons,
                    ];
                    $count++;
                    }
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
        
        $idArrray = json_decode($request['value']) ?? [];
        $termItems = [];

        foreach ($idArrray as $id) {
            $termItems[] = MedicalTerm::find($id);
        }
        $categoryOfServiceArray = array();
        $filteredTerms = collect($termItems)->groupBy(function ($term) use ($request, $categoryServiceNames, &$categoryOfServiceArray) {
            if (collect($term->has_term_types)->contains(function ($value) use ($request, $categoryServiceNames, &$categoryOfServiceArray) {
                $language = $this->get_available_language($value->name, locale());
                $name = json_decode($value->name)->$language;
                return in_array($name, $categoryServiceNames, true);
            })) {
                $name   = getTranslation($term->name, locale());
                $notes  = $term->note ? getTranslation($term->note, locale()) : null;
                $categoryOfServiceArray[$name] = [
                    'notes' => $notes,
                    'icons' => $term->term_icons,
                ];
                return $name;
            } else {
                $categoryOfServiceArray['No Category Of Service'] = [
                    'notes' => null,
                    'icons' => collect(),
                ];
                return 'No Category Of Service';
            }
        });
        return [
            'terms' => $filteredTerms,
            'termsArray' => $categoryOfServiceArray
        ];
    }

    public function linked_provider_type_terms($request, $providerTypeID, $providerID, $providerServicesArr = [])
    {
        $items = [];
        $idArrray = json_decode($request['value']) ?? [];
        $termItems = [];

        foreach ($idArrray as $id) {
            $termItems[] = MedicalTerm::find($id);
        }
        $count = 0;
        
        foreach ($termItems as $value) {
            $termTypes = $value->has_term_types;
            if (in_array($value->id, $providerServicesArr, true)) {
                if ($termTypes) {
                    $type = '';
                    foreach ($termTypes as $termType) {
                        if ($type !== $termType->term_type_name) {
                            $dateNow = date('Y-m-d');
                            $providerService = ProviderService::where('providers', $providerID)->where('services', $value->id)->first();
                            $notes = $providerService->service_item->note ? getTranslation($providerService->service_item->note,'en') : null;
                            if($providerService->day_end == '0000-00-00' || $dateNow < $providerService->day_end) {
                                $name = preg_replace('/<.+$/', '', $termType->term_type_name);
                                $items[$name][] = [
                                    'id' => $value->id,
                                    'name' => getTranslation($providerService->service_item->name, 'en'),
                                    'service_notes' => $notes,
                                    'parameter'     => $providerService->parameter_item == null ? null : $providerService->parameter_item->name,
                                    'images'        => $providerService->service_item->images,
                                    'description'   => $providerService->description,
                                    'services_icons'         => $providerService->service_item->term_icons->map(function ($termIcon) {
                                        return [
                                            'icon'              => $termIcon->icon,
                                            'provider_type'     => $termIcon->medical_term_provider_type->base_name,
                                        ];
                                    })
                                ]; 
                            }
                        }
                        // 'services' => $provider_service->map(function ($provider_service){
                        //     $dateNow = date('Y-m-d');
                        //     $notes = $provider_service->service_item->note ? getTranslation($provider_service->service_item->note,'en') : null;
                        //     if($provider_service->day_end == '0000-00-00' || $dateNow > $provider_service->day_end) return;
                        //     return [
                        //         'name'          => getTranslation($provider_service->service_item->name, 'en'),
                        //         // 'date_started'  => $provider_service->day_start,
                        //         // 'date_ended'    => $provider_service->day_end,
                        //         'service_notes' => $notes,
                        //         'parameter'     => $provider_service->parameter_item == null ? null : $provider_service->parameter_item->name,
                        //         'images'        => $provider_service->service_item->images,
                        //         'description'   => $provider_service->service_item->description,
                        //         'services_icons'         => $provider_service->service_item->term_icons->map(function ($termIcon) {
                        //             return [
                        //                 'icon'              => $termIcon->icon,
                        //                 'provider_type'     => $termIcon->medical_term_provider_type->base_name,
                        //             ];
                        //         })
                        //     ];
                        // $categoryOfServiceArray['No Category Of Service'] = [
                        //                 'name' => getTranslation($provider_service->service_item->name,'en'),
                        //                 'notes' => $provider_service->service_item->notes,
                        //                 'icons' => $provider_service->service_item->term_icons,
                        //             ];
                    }
                }
            } else if (count($providerServicesArr) == 0) {
                if ($termTypes) {
                    $type = '';
                    foreach ($termTypes as $termType) {
                        if ($type !== $termType->term_type_name) {
                            $name = preg_replace('/<.+$/', '', $termType->term_type_name);
                            $items[$name][] = [
                                'id' => $value->id,
                                'name' => $value->term_name,
                                'id_slug_name' => str_slug($value->base_name . '-' . $count),
                                'route_show' => str_contains($value->medical_terms, $providerTypeID) ? $value->route_show : 'disabled',
                            ];
                        }
                    }
                }
            }
        }
        return $items;
    }
}
