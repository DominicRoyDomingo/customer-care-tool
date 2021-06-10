<?php

namespace App\Models\Actions;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class Engagement extends Model
{
   protected $fillable = ['engagement'];
   protected $appends = ['is_loading', 'engagement_name', 'engagement_brands',
    'client_engagement_with_brand_names', 'engagement_brand_names', 'is_english', 'is_italian', 'is_german', 'is_filipino', 'is_visayan'];

   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->engagement);
   }

   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->engagement);
   }

   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->engagement);
   }

   public function getIsFilipinoAttribute()
   {
       return string_to_value('ph-fil', $this->activity);
   }

   public function getIsVisayanAttribute()
   {
       return string_to_value('ph-bis', $this->activity);
   }

   public function getEngagementNameAttribute()
   {
      $name = string_to_value(locale(), $this->engagement);

      $base_name = $this -> getIsEnglishAttribute();
      
      if (is_null($name)) {

         $mesage =  ' <small style="color:red">(No English translation yet)</small>';

         switch (locale()) {
            case 'it':
               $mesage = $base_name. ' <small style="color:red">(No Italian translation yet)</small>';
               break;
            case 'de':
               $mesage = $base_name. ' <small style="color:red">(No German translation yet)</small>';
               break;
            case 'ph-fil':
               $mesage = $base_name. ' <small style="color:red">(No Filipino translation yet)</small>';
               break;
            case 'ph-bis':
               $mesage = $base_name. ' <small style="color:red">(No Visayan translation yet)</small>';
               break;
         }

         $name = $mesage;
      }

      //$name .= ' - ' . $this->engagement_brand_names;

      return $name;
   }

   public function getClientEngagementWithBrandNamesAttribute(){
      return $this->engagement_name . " - " . $this->engagement_brand_names;
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }
   
   public function getEngagementBrandsAttribute() {
      $pool = [];
      $brands = json_decode($this->brands);
      
      if(isset($brands)){
         if(count($brands) > 0){
            foreach($brands as $brand_id){
               $brand = Brand::find($brand_id);
               $pool[] = $brand;
            }
         }
      }

      return $pool;
  }
   
  public function getEngagementBrandNamesAttribute() {
     $brands = $this->engagement_brands;
     $pool = [];
     
     foreach($brands as $brand){
        $pool[] = $brand->name;
     }

     $name = (count($pool) > 0) ? implode(", ", $pool) : "N/A";
     return $name;
 }
}
