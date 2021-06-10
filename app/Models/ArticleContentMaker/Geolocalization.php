<?php

namespace App\Models\ArticleContentMaker;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\Division;
use App\Models\City;
use App\Models\Region;
use App\Models\SyncReference;
use App\Models\ArticleContentMaker\GeolocalizeImage;
use App\Models\MedicalStuff\MedicalArticle;
use Laravel\Scout\Searchable;
use DB;

class Geolocalization extends Model
{
    use Searchable;

    protected $table = 'geolocalizations';

    protected $guarded = [];

    // public function searchableAs()
    // {   
    //     if(request()->getHttpHost() == 'stagingcct.med4care.online') {
    //         return config('scout.prefix').'geolocalization_staging';
    //     }
    //     return config('scout.prefix').'geolocalization';
    // }

    // public function toSearchableArray()
    // {
    //     if($this->article_item == null || $this->article_item->geolocalization_template == null) return;
    //     $provider_list = [];
    //     $tagsArr = [];
    //     $medicalArticle;

    //     $images = $this->geolocalize_images->map(function($item){
    //         return $item->image;
    //     });

    //     foreach ($this->article_item->image_slot as $imageSlot) {
    //         if(count($imageSlot->html_tag_items) != 0) {
    //             foreach ($imageSlot->html_tag_items as $tag) {
    //                 $tag["article_image_id"] = $imageSlot->id;
    //                 $tag["image"] = $imageSlot->image;
    //                 array_push($tagsArr, $tag);
    //             }
    //         }
    //     }
        
    //     $groupedTags = array_reduce($tagsArr, function($obj, $tag) {
    //         $obj[$tag->description][] = getTranslation($tag->image, 'en');
    //         return $obj;
    //     }, []);
    //     // dd($groupedTags);
    //     // $tagGroups = array_map(function($key, $tag) {
    //     //     return [
    //     //         'description' => $key,
    //     //         'images' => $tag
    //     //     ];
    //     // }, array_keys($groupedTags), $groupedTags);

    //     $division = $this->division_item != null ? $this->division_item->name.', ' : "";
    //     $region = $this->region_item != null ? $this->region_item->region.', ' : "";

    //     $place = null;
    //     switch ($this->area) {
    //         case 'City':
    //             $medicalArticle = DB::table('medical_term_article_link as mtl') 
    //                 -> leftJoin( 'provider_services as ps', 'ps.services', 'mtl.medical_term_id' )
    //                 -> leftJoin( 'providers as p', 'p.id', 'ps.providers' )
    //                 -> select( [ 'p.id as providers_id', 'p.name as providers_name', 'p.address as providers_address', 'p.contact_no as providers_contact_no'  ] )
    //                 -> where('mtl.medical_article_id', '=', $this->article )
    //                 -> where('p.country', '=', $this->country )
    //                 -> where('p.city', '=', $this->city )
    //                 -> where('p.division', '=', $this->division )
    //                 -> distinct();

    //             $provider_list = [];
    //             foreach( $medicalArticle -> get() as $prov ) {
    
    //                 $services = DB::table( 'provider_services' ) -> where( 'providers', '=', $prov -> providers_id ) -> get();
                    
    //                 $services_list = [];
    //                 foreach( $services as $serv ) {
    //                     // $service = DB::table( 'medical_terms' ) -> where( 'id', '=', $serv -> services ) -> select('name') -> first();
    
    //                     $medical_link = DB::table('medical_term_article_link as mtal') 
    //                                     -> leftJoin('medical_terms as mt', 'mt.id', 'mtal.medical_term_id') 
    //                                     -> where('mtal.medical_article_id', '=', $this->article )
    //                                     -> where('mt.id', '=', $serv -> services )
    //                                     -> select( [ 'mt.id', 'mt.name' ] )
    //                                     -> get();
                        
    //                     foreach( $medical_link as $medical_link_list ) {
    //                         $services_list[] = [
    //                         'name' => getTranslation( $medical_link_list-> name, 'en')
    //                         ];
    //                     }
                        
    //                 }
    //                 $newContact = [];
    //                 foreach(json_decode($prov->providers_contact_no, true) as $contact_no) {
    //                     $newContact[] = "<p>".$contact_no."</p>";
    //                 }
    //                 $provider_list[] = [
    //                     'id'            =>  $prov->providers_id,
    //                     'name'          =>  "<h4><b>".getTranslation($prov -> providers_name, 'en')."</b></h4>",
    //                     'address'       =>  "<p><i>".$prov->providers_address."</i></p>",
    //                     'contact_no'    =>  json_encode($newContact),
    //                 ];
    //             }

    //             $place = $this->city_item->name.', '. $division .$this->country_item->name;
    //            break;
    //         case 'Province':
    //             $medicalArticle = DB::table('medical_term_article_link as mtl') 
    //                 -> leftJoin( 'provider_services as ps', 'ps.services', 'mtl.medical_term_id' )
    //                 -> leftJoin( 'providers as p', 'p.id', 'ps.providers' )
    //                 -> select( [ 'p.id as providers_id', 'p.name as providers_name', 'p.address as providers_address', 'p.contact_no as providers_contact_no' ] )
    //                 -> where('mtl.medical_article_id', '=', $this->article )
    //                 -> where('p.country', '=', $this->country )
    //                 -> where('p.division', '=', $this->division );
                
    //             $provider_list = [];
    //             foreach( $medicalArticle -> get() as $prov ) {
    //             $provider_list[] = [
    //                     'id'            =>  $prov->providers_id,
    //                     'name'          =>  "<h4>".getTranslation($prov -> providers_name, 'en')."</h4>",
    //                     'address'       =>  "<p>".$prov->providers_address."</p>",
    //                     'contact_no'    =>  $prov->providers_contact_no,
    //                 ];
    //             }
    //             $place = $division .$this->country_item->name;
    //            break;
    //         case 'Region':
    //             $place = $region .$this->country_item->name;
    //            break;
    //     }
        
    //     $content = [
    //         'body'      => $this->article_item != null ? $this->article_item->geolocalization_template != null ? str_replace('[place]', $place, $this->article_item->geolocalization_template->content) : null : null,
    //         'images'    => $images->isEmpty() ? null : $images,
    //         'providers' => $provider_list,
    //         'html_tag_priorities' => $groupedTags,
    //     ];

    //     $slug = null;

    //     if($this->article_item != null) {
    //         if($this->article_item->geolocalization_template != null) {
    //             $newPlace = strtolower(preg_replace('/[ ,]+/', '-', $place));
    //             $slug = str_replace('[place]', $newPlace, $this->article_item->geolocalization_template->slug);
    //         }
    //     }

    //     $data = [
    //         'title' => $this->article_item != null ? $this->article_item->article_title : null,
    //         'meta_description' => $this->article_item != null ? $this->article_item->geolocalization_template != null ? str_replace('[place]', $place, $this->article_item->geolocalization_template->meta_description) : null : null,
    //         'slug' => $slug,
    //         'alt_tag_image' => $this->article_item != null ? $this->article_item->geolocalization_template != null ? str_replace('[place]', $place, $this->article_item->geolocalization_template->alt_tag_image) : null : null,
    //         'image_description' => $this->article_item != null ? $this->article_item->geolocalization_template != null ? str_replace('[place]', $place, $this->article_item->geolocalization_template->img_description) : null : null,
    //         'record_status' => $this->geolocalization_sync_reference->action == 'Update' ? 'recently updated' : 'added',
    //         'author' => null,
    //         'excerpt' => "",
    //         'content' => json_encode($content),
    //         'tags' => [],
    //         'url' => null,
    //         'custom_field' => false,
    //         'post_status' => $this->publish_status,
    //         'post_type' => "geolocalizations",
    //     ];
        
    //     return $data;
    // }

    public function country_item() {
        return $this->hasOne(Country::class, 'id', 'country');
    }

    public function division_item() {
        return $this->hasOne(Division::class, 'id', 'division');
    }

    public function city_item() {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function region_item() {
        return $this->hasOne(Region::class, 'id', 'regions');
    }

    public function geolocalize_images()
    {
        return $this->hasMany(GeolocalizeImage::class, 'place');
    }

    public function article_item() 
    {
        return $this->belongsTo(MedicalArticle::class, 'article');
    }

    public function geolocalization_sync_reference() {
        return $this->hasOne(SyncReference::class, 'table_id', 'id')->where('source_table', 'geolocalizations');
    }
}
