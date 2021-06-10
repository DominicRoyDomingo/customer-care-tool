<?php

namespace App\Models\Jobs;


use Illuminate\Database\Eloquent\Model;

use App\Models\Jobs\JobProfession;
use App\Models\MedicalStuff\Traits\TermTraits;

class JobDescription extends Model
{
   use TermTraits;

   protected $table = 'job_description';

   protected $fillable = ['description', 'job_profession_id'];

   protected $appends = [
      'is_loading',
      'is_english',
      'is_german',
      'is_italian',
      'is_filipino',
      'is_visayan',
      'is_bisaya',
      'base_name',
      'description_name',
      'on_select_description_name',
      'select_description_name',
      'has_translation'
   ];


   public function getBaseNameAttribute()
   {
      return $this->get_base_name();

      // $baseName = $this->is_english;

      // if (locale() === 'it')  $baseName = $this->is_italian;
      // if (locale() === 'de')  $baseName = $this->is_german;
      // if (locale() === 'ph-fil')  $baseName = $this->is_filipino;
      // if (locale() === 'ph-bis')  $baseName = $this->is_visayan;

      // if (!$baseName) {
      //    $priorityBaseName = $this->is_english ?? $this->is_italian;
      //    $baseName .= $priorityBaseName ?? $this->is_german;
      // }

      // return $baseName;
   }

   public function getHasTranslationAttribute()
   {
      $baseName = $this->base_name();

      if (!$baseName) {
         return false;
      }

      return true;
   }


   public function getOnSelectDescriptionNameAttribute()
   {
      return $this->description_name;
   }

   public function getDescriptionNameAttribute()
   {
      $language = language();
      $baseName = $this->is_english;

      if (locale() === 'it')  $baseName = $this->is_italian;
      if (locale() === 'de')  $baseName = $this->is_german;
      if (locale() === 'ph-fil')  $baseName = $this->is_german;
      if (locale() === 'ph-bis')  $baseName = $this->is_german;

      if (!$baseName) {
         $priorityBaseName = $this->is_english ?? $this->is_italian;
         $baseName .= $priorityBaseName ?? $this->is_german;
         $baseName .= "<span class='text-danger ml-1'>(No {$language} translation yet)</span>";
      }

      return $baseName;
   }

   public function getSelectDescriptionNameAttribute()
   {
      // $name = string_to_value(locale(), $this->description);

      // $base_name = $this->getIsEnglishAttribute();

      // if (is_null($name)) {
      //    $new = '';
      //    if( $this->getIsItalianAttribute() !== '' && $base_name == '' ){
      //       $new = $this->getIsItalianAttribute();
      //    }
      //    if( $this->getIsGermanAttribute() !== '' && $this->getIsItalianAttribute() == '' && $base_name == '' ){
      //       $new = $this->getIsGermanAttribute();
      //    }
      //    $mesage =  $new . ' (No English translation yet)';

      //    switch (locale()) {
      //       case 'it':
      //          $mesage = $base_name . ' (No Italian translation yet)';
      //          break;
      //       case 'de':
      //          $mesage = $base_name . ' (No German translation yet)';
      //          break;
      //    }

      //    $name = $mesage;
      // }

      // return $name;

      $name = string_to_value(locale(), $this->description);

      $base_name = $this->getIsEnglishAttribute();

      if (is_null($name)) {

         $new = '';
         $mesage = $this->description;
         if (json_decode($this->description) != null) {
            $availableLanguage = array_key_first(json_decode($this->description, true));
            $new = json_decode($this->description)->$availableLanguage;
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




   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->description);
   }

   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->description);
   }

   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->description);
   }

   public function getIsFilipinoAttribute()
   {
      return string_to_value('ph-fil', $this->category);
   }

   public function getIsVisayanAttribute()
   {
      return string_to_value('ph-bis', $this->category);
   }

   public function getIsBisayaAttribute()
   {
      return string_to_value('ph-bis', $this->category);
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }

   public function jobProfession()
   {
      return $this->belongsTo(JobProfession::class, 'job_profession_id');
   }
}
