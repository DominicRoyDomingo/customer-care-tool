<?php

namespace App\Models\MedicalStuff;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;
use App\Models\MedicalStuff\Traits\TermTraits;

class MedicalTermType extends Model
{
   use TermTraits;

   protected $guarded = [];

   protected $appends = [
      'term_type_name',
      'is_loading',
      'is_english',
      'is_italian',
      'is_german',
      'base_name',
      'is_service',
      'on_select_term_type_name',
      'has_translation',
      'base_language',
      'is_bisaya',
      'is_filipino',
   ];

   public function getHasTranslationAttribute()
   {
      $baseName = $this->base_name();

      if (!$baseName) {
         return false;
      }

      return true;
   }

   public function scopeActive($query)
   {
      return $query->join('brand_term_types', function ($join) {
         $join
            ->on('brand_term_types.term_type_id', '=', 'medical_term_types.id')
            ->where('brand_term_types.brand_id', request()->brand_id);
      });
   }

   public function getIsServiceAttribute()
   {
      return config('access.service_id') == $this->id;
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
         return 'de';
      }
      if ($this->is_filipino) {
         return 'de';
      }
   }
   /**
    * @return bool
    */
   public function getIsLoadingAttribute()
   {
      return false;
   }

   public function getOnSelectTermTypeNameAttribute()
   {
      return $this->term_type_name;
   }

   public function getBaseNameAttribute()
   {
      return $this->get_base_name();
   }

   public function getTermTypeNameAttribute()
   {
      return $this->get_base_name(true);
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


   public function isBelongToTerms(int $id)
   {
      $terms = MedicalTerm::pluck('term_types');

      if (!empty($terms)) {
         foreach ($terms as  $term) {
            if ($term) {
               foreach (string_to_value('term_types', $term) as $ttId) {
                  if ($id === $ttId) {
                     return true;
                  }
               }
            }
         }
      }

      return false;
   }

   public function brands()
   {
      return $this->belongsToMany(Brand::class, 'brand_term_types', 'term_type_id', 'brand_id');
   }
}
