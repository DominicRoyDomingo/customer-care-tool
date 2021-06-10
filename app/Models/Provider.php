<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Provider\ProviderActor;
use App\Models\Provider\ProviderAndItsType;
use App\Models\Provider\ProviderOtherInfo;
use App\Models\PaymentPlan\PaymentPlan;
use App\Models\MedicalStuff\MedicalTerm;
use CRM\API\MedicalStuff\Traits\MedicalStuffTrait;
use App\Models\Auth\User;

class Provider extends Model
{
    use Searchable;

    use MedicalStuffTrait;

    protected $table = 'providers';

    protected $guarded = [];

    protected $appends = ['selected_brands', 'provider_name', 'contact_nos'];

    public function searchableAs()
    {   
        $isStaging = request()->getHttpHost() == 'stagingcct.med4care.online';
        
        if($isStaging) {
            return request()->brand_name == 'Med4Care' ? config('scout.prefix').'provider_staging' : config('scout.prefix'). request()->brand_name .'_provider_staging';
        } else {
            return request()->brand_name == 'Med4Care' ? config('scout.prefix').'providers' : config('scout.prefix'). request()->brand_name .'_providers';
        }

    }

    public function toSearchableArray()
    {
        // if($this->id != 616) return;
        $objectID = 'App\Models\Provider::'.$this->id;
        $query = null;
        $postID = 0;
        $recordStatus = 'added';
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

        $provider_services = $this->provider_services;
        $categoryOfServiceArray = array();
        $otherInformations = array();
        $allServices = array();
        $servicesExclusive = null;
        $services = null;

        if($this->provider_sync_reference->action != 'New') {
            
            $provider = Provider::search($query, function ($algolia, $query, $options) use($objectID){
                $new_options = [];
                $new_options = ["facetFilters"=>"objectID:".$objectID];
                return $algolia->search($query, array_merge($options,$new_options));
            })->raw();
            if(isset($provider['hits'][0]['post_id'])) {
                $postID = $provider['hits'][0]['post_id'];
                $recordStatus = $provider['hits'][0]['record_status'];
            };
        };

        if ($recordStatus == 'sync') {

            $recordStatus = $this->provider_sync_reference->action == 'Update' ? 'recently updated' : 'added';

        }

        if ($recordStatus == null) {

            $recordStatus = 'added';

        }

        if(!$this->provider_other_infos->isEmpty()) {
            foreach ($this->provider_other_infos as $otherInfo) {

                $otherInfoArray = [
                    'type_of_info' => $otherInfo->type_item->base_name,
                    'value' => $otherInfo->value
                ];

                array_push($otherInformations, $otherInfoArray);

            }
        }
        if(!$provider_services->isEmpty()) {
            foreach ($this->provider_and_its_types as $providerAndItsType) {
                $providerType = $providerAndItsType->type_of_provider_item;
                $services = null;
                $categoryOfServiceArray = array();
                $categories = array();
                $servicesArr = array();
                $servicesExclusive = null;
                $providerServicesArr = $provider_services->pluck('services')->toArray();
                $request['value'] = $providerType->medical_terms;
                $request['parent_id'] = $providerType->id;
                $providerType->medical_terms_value = $this->group_terms($request);

                $withCategoryIds = array();
                foreach ($providerType->medical_terms_value['terms'] as $key => $terms) {
                    if ($key != 'No Category Of Service') array_push($categories, $key);

                    foreach ($terms as $key => $term) {
                        if (!$term->medical_terms) return;
                        
                        if (!str_contains($term->medical_terms, $providerType->id)) return;
                        $request['value'] = $term->medical_terms;
                        $request['parent_id'] = $term->id;
                        $term->medical_terms_value = $this->linked_provider_type_terms($request, $providerType->id, $this->id, $providerServicesArr);
                        foreach ($term->medical_terms_value as $key => $value) {
                            if (in_array($key, $serviceNames, true)) {
                                foreach ($value as $key => $service) {
                                    array_push($withCategoryIds, $service['id']);
                                }
                            }
                        }
                    }
                }
                $services = $providerType->medical_terms_value['terms']->map(function ($term, $key) use ($request, $providerServicesArr, $categoryServiceNames, $serviceNames, $providerTypeNames, $categories, $providerType, $withCategoryIds) {
                    if ($key == 'No Category Of Service') {
                    return $term->map(function ($medicalTerm) use ($request, $providerServicesArr, $categoryServiceNames, $serviceNames, $providerTypeNames, $categories, $providerType, $withCategoryIds) {
                            if (!in_array($medicalTerm->id, $providerServicesArr ,true)) return;
                            if (in_array($medicalTerm->id, $withCategoryIds, true)) return;
                            if (!$medicalTerm->medical_terms) return;
                            if (!str_contains($medicalTerm->medical_terms, $providerType->id)) return;
                            $request['value'] = $medicalTerm->medical_terms;
                            $request['parent_id'] = $medicalTerm->id;
                            $medicalTerm->medical_terms_value = $this->linked_provider_type_terms($request, $providerType->id, $this->id);
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
                                $dateNow = date('Y-m-d');
                                $providerService = ProviderService::where('providers', $this->id)->where('services', $medicalTerm->id)->first();
                                if($providerService->service_item->note != null) {
                                    if($providerService->service_item->note != "NULL") {
                                        if(request()->brand_name == 'Med4Care') {
                                            $providerService->service_item->note = getTranslationMed4CareBrand($providerService->service_item->note, locale());
                                        } else {
                                            $providerService->service_item->note = getTranslation($providerService->service_item->note, locale());
                                        }
                                    }
                                    $providerService->service_item->note = null;
                                }
                                $notes = $providerService->service_item->note;
                                if($providerService->day_end == '0000-00-00' || $dateNow < $providerService->day_end) {
                                    if(request()->brand_name == 'Med4Care') {
                                        $serviceName = getTranslationMed4CareBrand($medicalTerm->name, locale());
                                    } else {
                                        $serviceName = getTranslation($medicalTerm->name, locale());
                                    }
                                    return [
                                        'id'                => $medicalTerm->id,
                                        'name'              => $serviceName,
                                        'service_notes'     => $notes,
                                        'parameter'         => $providerService->parameter_item == null ? null : $providerService->parameter_item->name,
                                        'images'            => $providerService->service_item->images,
                                        'description'       => $providerService->description,
                                        'services_icons'    => $medicalTerm->icon_url,
                                    ];
                                }
                                return;
                            }
                    })->filter(function ($value, $key) {
                        return $value != null;
                    })->values();
                    } else {
                        if (!$term[0]->medical_terms) return;
                    
                        if (!str_contains($term[0]->medical_terms, $providerType->id)) return;
                        $request['value'] = $term[0]->medical_terms;
                        $request['parent_id'] = $term[0]->id;
                        $term[0]->medical_terms_value = $this->linked_provider_type_terms($request, $providerType->id, $this->id, $providerServicesArr);
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
                })->map(function ($term, $categoryOfService) use(&$categoryOfServiceArray, $providerType) {
                    return [
                        'category_of_service'       => $categoryOfService,
                        'category_notes'            => $providerType->medical_terms_value['termsArray'][$categoryOfService]['notes'], 
                        'category_icons'            => $providerType->medical_terms_value['termsArray'][$categoryOfService]['icon'],
                        'services' => $term,
                    ];
                })->filter(function ($value, $key) {
                    return $value != null;
                })->values();

                $servicesArr['type_of_provider'] = getTranslation($providerAndItsType->type_of_provider_item->name, 'en');
                $servicesArr['provider_services'] = $services;
                array_push($allServices, $servicesArr);
            }
            
        }
        
        $content = [
            'name'                  => getTranslation($this->name,'en'),
            'country'               => $this->country == null ? null : ["name"  => $this->country_item->name],
            'city'                  => $this->city == null ? null : ["name"  => $this->city_item->name],
            'division'              => $this->division == null ? null : ["name"  => $this->division_item->name],
            'address'               => $this->address,
            'contact_no'            => $this->contact_no,
            'website'               => $this->website,
            'images'                => $this->images,
            'facebook_url'          => $this->facebook_url,
            'linkedin'              => $this->linkedin,
            'account_status'        => $this->account_status,
            'other_info'            => $otherInformations,
            'all_services'          => $allServices,
            'brand'                 => request()->brand_name,
        ];
        $newContent = preg_replace("/\\\\u([0-9a-fA-F]{4})/", "&#x\\1;", json_encode($content));
       
        // Convert the entities to a UTF-8 string
        $newContent = html_entity_decode($newContent, ENT_QUOTES, 'UTF-8');

        $data = [
            'record_status' => $recordStatus,
            'title' => getTranslation($this->name,'en'),
            'author' => null,
            'excerpt' => "",
            'content' => $newContent,
            'tags' => [],
            'url' => null,
            'custom_field' => false,
            'post_status' => "publish",
            'post_type' => "providers",
            'post_id' => $postID,
        ];
        
        return $data;
    }

    // public static function boot()
    // {
    //     parent::boot();

    //     static::saved(function ($model) {
    //         $model->provider_services->filter(function ($item) {
    //             return $item->shouldBeSearchable();
    //         })->searchable();
    //     });
    // }

    public function provider_type_item()
    {
        return $this->hasOne(MedicalTerm::class, 'id', 'provider_type');
    }

    public function provider_group_item()
    {
        return $this->hasOne(ProviderGroup::class, 'id', 'group_id');
    }

    public function providers_brand_item()
    {
        return $this->hasOne(ProvidersBrand::class, 'provider', 'id');
    }

    public function provider_services()
    {
        return $this->hasMany(ProviderService::class, 'providers', 'id');
    }

    public function provider_and_its_types()
    {
        return $this->hasMany(ProviderAndItsType::class, 'provider', 'id');
    }

    public function provider_and_its_type_items()
    {
        return $this->belongsToMany(MedicalTerm::class, 'provider_and_its_type', 'provider', 'type_of_provider');
    }

    public function provider_actors()
    {
        return $this->hasMany(ProviderActor::class, 'provider', 'id');
    }

    public function provider_other_infos()
    {
        return $this->hasMany(ProviderOtherInfo::class, 'provider', 'id');
    }

    public function provider_profiles()
    {
        return $this->hasOne(ProviderProfile::class, 'provider', 'id');
    }

    public function provider_sync_reference()
    {
        return $this->hasOne(SyncReference::class, 'table_id', 'id')->where('source_table', 'providers');
    }

    public function payment_plan()
    {
        return $this->hasOne(PaymentPlan::class, 'id', 'plan');
    }

    public function country_item()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }

    public function division_item()
    {
        return $this->hasOne(Division::class, 'id', 'division');
    }

    public function city_item()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function created_by_item()
    {
        return $this->hasOne(User::class, 'id', 'created_by')->latest();
    }

    public function last_modified_by_item()
    {
        return $this->hasOne(User::class, 'id', 'last_modified_by')->latest();
    }

    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->name);
    }

    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->name);
    }

    public function getIsItalianAttribute()
    { 
        return string_to_value('it', $this->name);
    }

    public function getProviderNameAttribute()
    {

        $name = string_to_value(locale(), $this->name);
        // dd(locale());
        $base_name = $this->getIsEnglishAttribute();

        if (is_null($name)) {
            //   dd(json_decode($this->name));
            $new = '';
            $mesage = $this->name;
            if(json_decode($this->name) != null) {
                $availableLanguage = array_key_first(json_decode($this->name, true));
                $new = json_decode($this->name)->$availableLanguage;
                $mesage =  $new; 
                // . ' (No English translation yet)';

                switch (locale()) {
                case 'it':
                    $mesage = $new;
                    //  . ' (No Italian translation yet)';
                    break;
                case 'de':
                    $mesage = $new;
                    //  . ' (No German translation yet)';
                    break;
                case 'ph-fil':
                    $mesage = $new;
                    //  . ' (No Filipino translation yet)';
                    break;
                case 'ph-bis':
                    $mesage = $new;
                    //  . ' (No Visayan translation yet)';
                    break;
                }
            }
            

            $name = $mesage;
        }
        return $name;
    }

    public function getSelectedBrandsAttribute()
    {

        $brands = ProvidersBrand::where('provider', $this->id)->get();
        $brandsArr = array();
        foreach ($brands as $brand) {
            array_push($brandsArr, $brand->brands);
        }
        return $brandsArr;
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
                if(request()->brand_name == 'Med4Care') {
                    $name = getTranslationMed4CareBrand($term->name, locale());
                } else {
                    $name = getTranslation($term->name, locale());
                }
                if($term->note != null) {
                    if($term->note != "NULL") {
                        if(request()->brand_name == 'Med4Care') {
                            $term->note = getTranslationMed4CareBrand($term->note, locale());
                        } else {
                            $term->note = getTranslation($term->note, locale());
                        }
                        
                    }
                    $term->note = null;
                }
                
                $notes  = $term->note;
                $categoryOfServiceArray[$name] = [
                    'notes' => $notes,
                    'icon'  => $term->icon_url,
                ];
                return $name;
            } else {
                $categoryOfServiceArray['No Category Of Service'] = [
                    'notes' => null,
                    'icon'  => null,
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
                            if($providerService->service_item->note != null) {
                                if($providerService->service_item->note != "NULL") {
                                    if(request()->brand_name == 'Med4Care') {
                                        $providerService->service_item->note = getTranslationMed4CareBrand($providerService->service_item->note, locale());
                                    } else {
                                        $providerService->service_item->note = getTranslation($providerService->service_item->note, locale());
                                    }
                                }
                                $providerService->service_item->note = null;
                            }
                            $notes = $providerService->service_item->note;
                            if($providerService->day_end == '0000-00-00' || $dateNow < $providerService->day_end) {
                                $name = preg_replace('/<.+$/', '', $termType->term_type_name);
                                if(request()->brand_name == 'Med4Care') {
                                    $serviceName = getTranslationMed4CareBrand($providerService->service_item->name, 'en');
                                } else {
                                    $serviceName = getTranslation($providerService->service_item->name, 'en');
                                }
                                
                                $items[$name][] = [
                                    'id'                => $value->id,
                                    'name'              => $serviceName,
                                    'service_notes'     => $notes,
                                    'parameter'         => $providerService->parameter_item == null ? null : $providerService->parameter_item->name,
                                    'images'            => $providerService->service_item->images,
                                    'description'       => $providerService->description,
                                    'services_icons'    => $providerService->service_item->icon_url,
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

    public function getContactNosAttribute()
    {
        return json_decode($this->contact_no);
    }

    // public function linked_terms($request, $providerTypeId)
    // {   
    //     $idArrray  = json_decode($request['value']) ?? [];
    //     $items     = [];
    //     $termItems = [];
    //     $termItems = collect($idArrray)->map(function ($id) {
    //         return  MedicalTerm::find($id);
    //     });

    //     foreach ($termItems as $term) {
    //         if(in_array($providerTypeId, json_decode($term->medical_terms))) {
    //             if ($termTypes = $term->has_term_types) {
    //                 $count = 0;
    //                 foreach ($termTypes as $termType) {
    //                 $items[preg_replace('/<.+$/', '',$termType->term_type_name)][] = [
    //                     'id' => $term->id,
    //                     'name' => $term->term_name,
    //                     'id_slug_name' => str_slug("{$term->base_name} {$count}"),
    //                     'descriptions' => $this->linked_term_description((int)$request['parent_id'], (int)$term->id),
    //                     'route_show' => $term->route_show,
    //                     'icons' => $term->term_icons,
    //                 ];
    //                 $count++;
    //                 }
    //             }
    //         }
    //     }

    //     return $items;
    // }
}
