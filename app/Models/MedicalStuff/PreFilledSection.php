<?php

namespace App\Models\MedicalStuff;

use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\Traits\TermTraits;

use App\Models\ItemType;
use App\Models\MedicalStuff\Traits\Scope\PreFilledSectionScope;

class PreFilledSection extends Model
{
    use TermTraits ,PreFilledSectionScope;

    protected $table = 'pre_filled_section';
    
    protected $guarded = [];

    protected $appends = [
        'section_name',
        'is_loading',
        'is_english',
        'is_german',
        'is_italian',
        'is_filipino',
        'is_visayan',
        'is_bisaya',
        'base_name',
        'base_language',
        'has_translation'
    ];

    public function getSectionNameAttribute()
    {
       return $this->get_base_name(true);
    }

    public function getBaseNameAttribute()
    {
       return $this->get_base_name();
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

        if ($this->is_filipino) {
            return 'ph-fil';
        }

        if ($this->is_bisaya) {
            return 'ph-bis';
        }
    }

    public function getHasTranslationAttribute()
    {
       $baseName = $this->base_name();
 
       if (!$baseName) {
          return false;
       }
 
       return true;
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

   public function getIsFilipinoAttribute()
   {
      return string_to_value('ph-fil', $this->name);
   }

   public function getIsVisayanAttribute()
   {
      return string_to_value('ph-bis', $this->name);
   }

   public function getIsBisayaAttribute()
   {
      return string_to_value('ph-bis', $this->name);
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }

    public function tags(){
        return $this->belongsTo(HtmlTag::class, 'tags_used_id');
    }
    
    public function item_type(){
        return $this->belongsTo(ItemType::class, 'type_of_publishing_item_id');
    }
}
