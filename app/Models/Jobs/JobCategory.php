<?php

namespace App\Models\Jobs;

use App\Models\Brand\BrandCategory;
use App\Models\Jobs\JobCategoryProfession;
use App\Models\Jobs\Scopes\JobCategoryScope;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
   use JobCategoryScope;

   protected $table = 'job_category';

   protected $fillable = ['category'];

   protected $appends = ['is_loading', 'category_name', 'is_english', 'is_italian', 'is_german', 'is_filipino', 'is_visayan', 'add_new', 'profession_ids'];

   public function getProfessionIdsAttribute()
   {
      $id_pool = [];

      foreach ($this->jobCategoryProfession as $job_category_profession_link) {
         if (isset($job_category_profession_link->job_profession_id))
            $id_pool[] = $job_category_profession_link->job_profession_id;
      }

      return $id_pool;
   }

   public function getAddNewAttribute()
   {
      return 'add_new';
   }

   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->category);
   }

   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->category);
   }

   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->category);
   }

   public function getIsFilipinoAttribute()
   {
      return string_to_value('ph-fil', $this->category);
   }

   public function getIsVisayanAttribute()
   {
      return string_to_value('ph-bis', $this->category);
   }

   public function getCategoryNameAttribute()
   {


      $name = string_to_value(locale(), $this->category);

      $base_name = $this->getIsEnglishAttribute();

      if (is_null($name)) {

         $new = '';
         $mesage = $this->category;
         if (json_decode($this->category) != null) {
            $availableLanguage = array_key_first(json_decode($this->category, true));
            $new = json_decode($this->category)->$availableLanguage;
            $mesage =  $new . ' (No English translation yet)';

            switch (locale()) {
               case 'it':
                  $mesage = $new . ' (No Italian translation yet)';
                  break;
               case 'de':
                  $mesage = $new . ' (No German translation yet)';
                  break;
               case 'ph-fil':
                  $mesage = $new . ' (No Filipino translation yet)';
                  break;
               case 'ph-bis':
                  $mesage = $new . ' (No Visayan translation yet)';
                  break;
            }
         }


         $name = $mesage;
      }
      return $name;
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }

   public function jobCategoryProfession()
   {
      return $this->hasMany(JobCategoryProfession::class, 'job_category_id');
   }

   public function brandCategory()
   {
      return $this->hasMany(BrandCategory::class, 'category');
   }
}
