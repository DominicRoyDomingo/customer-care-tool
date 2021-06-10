<?php

namespace App\Models\Courses\Traits\Attributes;

/**
 * Course Type Attribute 
 */
trait CourseTypeAttribute
{
   function base_name()
   {
      $baseName = $this->is_english;
      if (locale() === 'it')  $baseName = $this->is_italian;
      if (locale() === 'de')  $baseName = $this->is_german;
      if (locale() === 'ph-fil')  $baseName = $this->is_filipino;
      if (locale() === 'ph-bis')  $baseName = $this->is_bisaya;

      return $baseName;
   }

   public function get_base_name($key = false)
   {
      $baseName = $this->base_name();

      if (!$baseName) {
         $priorityBaseName = $this->is_english ?? $this->is_italian;
         $priorityBaseName = $priorityBaseName ?? $this->is_german;
         $priorityBaseName = $priorityBaseName ?? $this->is_filipino;
         $baseName .= $priorityBaseName ?? $this->is_bisaya;

         if ($key) {
            $language = language();
            $baseName .= "<i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
         }
      }

      return $baseName;
   }


   public function getBaseLanguageAttribute()
   {
      if ($this->is_english) {
         return 'en';
      }

      if ($this->is_italian) {
         return 'it';
      }

      if ($this->is_german) {
         return 'de';
      }

      if ($this->is_bisaya) {
         return 'ph-bis';
      }

      if ($this->is_filipino) {
         return 'ph-fil';
      }
   }

   /**
    * @return String
    */
   public function getIsEnglishAttribute()
   {
      return string_to_value('en', $this->name);
   }

   /**
    * @return String
    */
   public function getIsGermanAttribute()
   {
      return string_to_value('de', $this->name);
   }

   /**
    * @return String
    */
   public function getIsItalianAttribute()
   {
      return string_to_value('it', $this->name);
   }

   public function getIsBisayaAttribute()
   {
      return string_to_value('ph-bis', $this->name);
   }

   public function getIsFilipinoAttribute()
   {
      return string_to_value('ph-fil', $this->name);
   }
}
