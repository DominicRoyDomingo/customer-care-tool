<?php

namespace App\Models\Jobs;

use App\Models\Jobs\JobCategoryProfession;

use App\Models\Jobs\JobDescription;
use App\Models\Jobs\Scopes\ProfessionScope;
use Illuminate\Database\Eloquent\Model;

class JobProfession extends Model
{
   use ProfessionScope;

   protected $table = 'job_profession';

   protected $fillable = ['profession'];

   protected $appends = [
      'is_loading',
      'profession_name',
      'base_name',
      'is_english',
      'is_german',
      'is_italian',
      'is_filipino',
      'is_visayan',
      'categories_title',
      'category_ids',
      'description_ids',
      'on_select_profession_name',
      'select_profession_name'
   ];

   public function getBaseNameAttribute()
   {
      $baseName = $this->is_english;

      if (locale() === 'it')  $baseName = $this->is_italian;
      if (locale() === 'de')  $baseName = $this->is_german;

      if (!$baseName) {
         $priorityBaseName = $this->is_english ?? $this->is_italian;
         $baseName .= $priorityBaseName ?? $this->is_german;
      }

      return $baseName;
   }

   public function getProfessionNameAttribute()
   {

      $name = string_to_value(locale(), $this->profession);

      $base_name = $this->getIsEnglishAttribute();

      if (is_null($name)) {

         if ($this->getIsItalianAttribute() !== '' && $base_name == '') {
            $base_name = $this->getIsItalianAttribute();
         } else if ($this->getIsGermanAttribute() !== '' && $base_name == '') {
            $base_name = $this->getIsGermanAttribute();
         } else if ($this->getIsFilipinoAttribute() !== '' && $base_name == '') {
            $base_name = $this->getIsFilipinoAttribute();
         } else if ($this->getIsVisayanAttribute() !== '' && $base_name == '') {
            $base_name = $this->getIsVisayanAttribute();
         }
         $mesage =  $base_name . ' (No English translation yet)';

         switch (locale()) {
            case 'it':
               $mesage = $base_name . ' (No Italian translation yet)';
               break;
            case 'de':
               $mesage = $base_name . ' (No German translation yet)';
               break;
            case 'ph-fil':
               $mesage = $base_name . ' (No Filipino translation yet)';
               break;
            case 'ph-bis':
               $mesage = $base_name . ' (No Visayan translation yet)';
               break;
         }

         $name = $mesage;
      }

      return $name;
   }

   public function getSelectProfessionNameAttribute()
   {
      $name = string_to_value(locale(), $this->profession);

      $base_name = $this->getIsEnglishAttribute();

      if (is_null($name)) {

         $new = '';
         $mesage = $this->profession;
         if (json_decode($this->profession) != null) {
            $availableLanguage = array_key_first(json_decode($this->profession, true));
            $new = json_decode($this->profession)->$availableLanguage;
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

   public function getOnSelectProfessionNameAttribute()
   {
      return $this->profession_name;
   }

   public function getCategoryIdsAttribute()
   {
      $id_pool = [];

      foreach ($this->jobCategoryProfession as $job_category_profession_link) {
         if (isset($job_category_profession_link->job_category_id))
            $id_pool[] = $job_category_profession_link->job_category_id;
      }

      return $id_pool;
   }

   public function getDescriptionIdsAttribute()
   {
      $id_pool = [];

      foreach ($this->jobDescriptions as $job_profession_description_link) {
         if (isset($job_profession_description_link->job_description_id))
            $id_pool[] = $job_profession_description_link->job_description_id;
      }

      return $id_pool;
   }

   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->profession);
   }

   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->profession);
   }


   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->profession);
   }

   public function getIsFilipinoAttribute()
   {
      return string_to_value('ph-fil', $this->category);
   }

   public function getIsVisayanAttribute()
   {
      return string_to_value('ph-bis', $this->category);
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }

   public function getCategoriesTitleAttribute()
   {
      $category = $this->job_category_profession;

      // if ($category->count() < 1) {
      //    return '<i class="text-muted ">No data available.</i>';
      // }

      // $li = '';

      // foreach ($category as $cat) {
      //    $li .= $cat->job_category->category_name;
      // }
      return $category;
      // return string_to_value('en', $this->profession);
   }


   public function jobCategoryProfession()
   {
      return $this->hasMany(JobCategoryProfession::class, 'job_profession_id');
   }

   public function jobDescriptions()
   {
      return $this->hasMany(JobDescription::class, 'job_profession_id');
   }
}
