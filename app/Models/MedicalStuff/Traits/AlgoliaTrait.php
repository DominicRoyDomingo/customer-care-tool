<?php
namespace App\Models\MedicalStuff\Traits;

use App\Models\MedicalStuff\MedicalArticle;
use DB;

/**
 * Algolia Traits for Medical Articles
 */
trait AlgoliaTrait
{
    public function setSearchableGeolocalizedArray(){
        $objectID = 'App\Models\MedicalStuff\MedicalArticle::' . $this->id;
        $contents = array();
        $query = null;
        $oldContentsArray = array();
        $recordStatus = 'added';
        if ($this->geolocalization_sync_reference->action != 'New') {
            $provider = MedicalArticle::search($query, function ($algolia, $query, $options) use ($objectID) {
                $new_options = [];
                $new_options = ["facetFilters" => "objectID:" . $objectID];
                return $algolia->search($query, array_merge($options, $new_options));
            })->raw();

            if (isset($provider['hits'][0]['content'])) {
                $oldContentsArray = $provider['hits'][0]['content'];
                $recordStatus = $provider['hits'][0]['record_status'];
            };
        };

        if ($recordStatus == 'sync') {

            $recordStatus = $this->geolocalization_sync_reference->action == 'Update' ? 'recently updated' : 'added';

        }

        if ($recordStatus == null) {

            $recordStatus = 'added';

        }

        foreach ($this->geolocalizations as $geolocalization) {
            if ($this == null || $this->geolocalization_template == null) return;
            $provider_list = [];
            $tagsArr = [];
            $medicalArticle = '';

            $images = $geolocalization->geolocalize_images->map(function ($item) {
                return $item->image;
            });

            foreach ($this->image_slot as $imageSlot) {
                if (count($imageSlot->html_tag_items) != 0) {
                    foreach ($imageSlot->html_tag_items as $tag) {
                        $tag["article_image_id"] = $imageSlot->id;
                        $tag["image"] = $imageSlot->image;
                        array_push($tagsArr, $tag);
                    }
                }
            }

            $groupedTags = array_reduce($tagsArr, function ($obj, $tag) {
                $obj[$tag->description][] = getTranslation($tag->image, 'en');
                return $obj;
            }, []);

            $division = $geolocalization->division_item != null ? $geolocalization->division_item->name . ', ' : "";
            $region = $geolocalization->region_item != null ? $geolocalization->region_item->region . ', ' : "";

            $place = null;
            switch ($geolocalization->area) {
                case 'City':
                    $medicalArticle = DB::table('medical_term_article_link as mtl')
                        ->leftJoin('provider_services as ps', 'ps.services', 'mtl.medical_term_id')
                        ->leftJoin('providers as p', 'p.id', 'ps.providers')
                        ->select(['p.id as providers_id', 'p.name as providers_name', 'p.address as providers_address', 'p.contact_no as providers_contact_no'])
                        ->where('mtl.medical_article_id', '=', $geolocalization->article)
                        ->where('p.country', '=', $geolocalization->country)
                        ->where('p.city', '=', $geolocalization->city)
                        ->where('p.division', '=', $geolocalization->division)
                        ->distinct();

                    $provider_list = [];
                    foreach ($medicalArticle->get() as $prov) {

                        $services = DB::table('provider_services')->where('providers', '=', $prov->providers_id)->get();

                        $services_list = [];
                        foreach ($services as $serv) {
                            // $service = DB::table( 'medical_terms' ) -> where( 'id', '=', $serv -> services ) -> select('name') -> first();

                            $medical_link = DB::table('medical_term_article_link as mtal')
                                ->leftJoin('medical_terms as mt', 'mt.id', 'mtal.medical_term_id')
                                ->where('mtal.medical_article_id', '=', $geolocalization->article)
                                ->where('mt.id', '=', $serv->services)
                                ->select(['mt.id', 'mt.name'])
                                ->get();

                            foreach ($medical_link as $medical_link_list) {
                                $services_list[] = [
                                    'name' => getTranslation($medical_link_list->name, 'en')
                                ];
                            }
                        }
                        $newContact = [];
                        foreach (json_decode($prov->providers_contact_no, true) as $contact_no) {
                            $newContact[] = "<p>" . $contact_no . "</p>";
                        }
                        $provider_list[] = [
                            'id'            =>  $prov->providers_id,
                            'name'          =>  "<h4><b>" . getTranslation($prov->providers_name, 'en') . "</b></h4>",
                            'address'       =>  "<p><i>" . $prov->providers_address . "</i></p>",
                            'contact_no'    =>  json_encode($newContact),
                        ];
                    }

                    $place = $geolocalization->city_item->name;
                    break;
                case 'Province':
                    $medicalArticle = DB::table('medical_term_article_link as mtl')
                        ->leftJoin('provider_services as ps', 'ps.services', 'mtl.medical_term_id')
                        ->leftJoin('providers as p', 'p.id', 'ps.providers')
                        ->select(['p.id as providers_id', 'p.name as providers_name', 'p.address as providers_address', 'p.contact_no as providers_contact_no'])
                        ->where('mtl.medical_article_id', '=', $geolocalization->article)
                        ->where('p.country', '=', $geolocalization->country)
                        ->where('p.division', '=', $geolocalization->division);

                    $provider_list = [];
                    foreach ($medicalArticle->get() as $prov) {
                        $provider_list[] = [
                            'id'            =>  $prov->providers_id,
                            'name'          =>  "<h4>" . getTranslation($prov->providers_name, 'en') . "</h4>",
                            'address'       =>  "<p>" . $prov->providers_address . "</p>",
                            'contact_no'    =>  $prov->providers_contact_no,
                        ];
                    }
                    $place = $division;
                    break;
                case 'Region':
                    $place = $region;
                    break;
            }

            $content = [
                'body'      => $this != null ? $this->geolocalization_template != null ? str_replace('[place]', $place, $this->geolocalization_template->content) : null : null,
                'images'    => $images->isEmpty() ? null : $images,
                'providers' => $provider_list,
                'html_tag_priorities' => $groupedTags,
            ];

            $slug = null;

            if ($this != null) {
                if ($this->geolocalization_template != null) {
                    $newPlace = strtolower(preg_replace('/[ ,]+/', '-', $place));
                    $slug = str_replace('[place]', $newPlace, $this->geolocalization_template->slug);
                }
            }

            $geolocalizedArray = [
                'post_id' => 0,
                'geolocalized_id' => $geolocalization->id,
                'title' => $this != null ? str_replace('[place]', $place, $this->base_name) : null,
                'meta_description' => $this != null ? $this->geolocalization_template != null ? str_replace('[place]', $place, $this->geolocalization_template->meta_description) : null : null,
                'slug' => $slug,
                'alt_tag_image' => $this != null ? $this->geolocalization_template != null ? str_replace('[place]', $place, $this->geolocalization_template->alt_tag_image) : null : null,
                'image_description' => $this != null ? $this->geolocalization_template != null ? str_replace('[place]', $place, $this->geolocalization_template->img_description) : null : null,
                'body' => $content,
            ];
            
            array_push($contents, $geolocalizedArray);
        }
        
        $data = [
            'content' => $contents,
            'record_status' => $recordStatus,
            'author' => null,
            'excerpt' => "",
            'tags' => [],
            'url' => null,
            'custom_field' => false,
            'post_status' => $geolocalization->publish_status,
            'post_type' => "geolocalizations",
        ];

        return $data;
    } 

    public function setSearchableCourseArray(){
        $data =[
            'objectID' => $this->id,
            'title' => $this->title,
            'meta_description' => $this->publishing_item_content->meta_description ?? null,
            'slug' => $this->publishing_item_content->slug ?? null,
            'alt_tag_image' => $this->publishing_item_content->alt_tag_image ?? null,
            'image_description' => $this->publishing_item_content->img_description ?? null,
            'record_status' => $this->course_sync_reference->action == 'Update' ? 'added' : 'recently updated',
            'brand' => self::brandNameToArray($this->brands),
            'content' => [
                'body' => $this->publishing_item_content->content ?? null,
                'price' => $this->course_info->price ?? null,
                'ecm_credits' => $this->course_info->number_credit ?? null,
                'duration' => $this->course_info->time_duration ?? null,
                'address' => $this->course_info->address ?? null,
                'course_type' => self::courseTypeToArray($this->course_info),
                'other_info' => self::otherInfoToArray($this->course_information_types),
                'authors' => self::articleAuthorsToArray($this->article_authors),
                'disciplin_ecm' =>  self::disciplinECMToArray($this->course_discipline),
                'destinatari' => self::destinatariToArray($this->course_reciepient)
            ]
        ];

        return $data;
    } 

    protected static function disciplinECMToArray($object){
        $discipline = [];
        
        if($object){
            foreach($object as $obj){
                $data = [
                    'name' => $obj->term->name,
                    'icon' => $obj->term->icon_url,
                ];
    
                array_push($discipline, $data);
            }
        }


        return $discipline;
    }

    protected static function destinatariToArray($object){
        $recepient = [];
        
        if($object){
            foreach($object as $obj){
                $data = [
                    'name' => $obj->term->name,
                    'icon' => $obj->term->icon_url,
                ];
    
                array_push($recepient, $data);
            }
        }

        return $recepient;
    }

    protected static function otherInfoToArray($object){
        $other_infos = [];

        foreach($object as $obj){
            $data = [
                'information_type' => $obj->information_type_id,
                'value' => $obj->information,
            ];

            array_push($other_infos, $data);
        }

        return $other_infos;
    }

    protected static function brandNameToArray($object){
        $brands = [];

        foreach($object as $obj){
            array_push($brands, $obj->name);
        }

        return $brands;
    }

    protected static function courseTypeToArray($object){
        $course_types = [];

        if($object){
            foreach($object->has_course_types as $obj){
                array_push($course_types, $obj->base_name);
            }
        }
     
        return $course_types;
    }

    protected static function articleAuthorsToArray($object){
        $authors = [];
        
        if($object){
            $author_type = $object->type->name;
            foreach($object->actors as $obj){
                $data = [
                    'author_type' => $author_type,
                    'name' => [
                        'surname' => $obj->surname,
                        'firstname' => $obj->firstname,
                        'middlename' => $obj->middlename,
                    ],
                ];
                array_push($authors, $data);
            }
        }
     
        return $authors;
    }

}
