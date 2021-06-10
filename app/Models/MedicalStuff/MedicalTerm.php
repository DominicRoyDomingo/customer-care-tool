<?php

namespace App\Models\MedicalStuff;

use App\Models\Auth\User;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\MedicalStuff\Icon;
use App\Models\MedicalStuff\Traits\TermTraits;
use App\Models\Service;
use App\Models\ProviderService;
use App\Models\MedicalStuff\Traits\Attributes\TermAttribute;
use App\Models\MedicalStuff\Traits\Scope\TermScope;
use DB;

class MedicalTerm extends Model
{
   use TermTraits, TermAttribute, TermScope;

   protected $guarded = [];

   protected $appends = [
      'term_name',
      'term_note',
      'base_term_note',
      'is_loading',
      'is_english',
      'is_italian',
      'is_german',
      'is_bisaya',
      'is_filipino',
      'has_specializations',
      'has_term_types',
      'has_translation',
      'has_descriptions',
      'has_terms_id',
      'is_linked_term',
      'is_linked_to_article',
      'is_rendered',
      'base_name',
      'base_language',
      'route_show',
      'img_url',
   ];

   public function getIsRenderedAttribute()
   {
      return true;
   }

   public function getIsLinkedToArticleAttribute()
   {
      if (!request()->article_id) {
         return false;
      }

      $articleId = request()->article_id;

      $article = MedicalArticle::findOrFail($articleId);

      if ($article) {
         foreach ($article->medical_terms as $term) {
            if ($term->id === $this->id) {
               return true;
            }
         }
      }

      return false;
   }

   public function getHasTermTypesAttribute()
   {
      if (!$this->term_types) {
         return null;
      }

      if (!string_to_value('term_types', $this->term_types)) {
         return null;
      }

      $items = [];

      foreach (string_to_value('term_types', $this->term_types) as $id) {
         if ($id && $termType = MedicalTermType::find($id)) {
            $items[] = $termType;
         }
      }

      return $items;
   }

   public function getHasTranslationAttribute()
   {
      $baseName = $this->base_name();

      if (!$baseName) {
         return false;
      }

      return true;
   }

   public function getImgUrlAttribute()
   {
      return $this->image ?: null;
   }

   public function getIsLinkedTermAttribute()
   {
      $terms = request()->medical_terms;

      if (!$terms) {
         return false;
      }

      foreach (json_decode($terms) as $id) {
         if ($id == $this->id) return true;
      }

      return false;
   }

   public function getHasDescriptionsAttribute()
   {
      if (!$this->medical_terms) {
         return [];
      }

      $parentId = (int) request()->parent_id;

      if (!$parentId) {
         return [];
      }

      return TermConnectionDescription::where('term_id', $parentId)
         ->where('linked_term_id', $this->id)
         ->with(['term_description'])
         ->get();
   }

   public function getBaseNameAttribute()
   {
      return $this->get_base_name();
   }
   public function getBaseTermNoteAttribute()
   {
      return $this->base_note_name();
   }
   public function getTermNoteAttribute()
   {
      return $this->base_note_name(true);
   }

   public function getTermNameAttribute()
   {
      return $this->get_base_name(true);
   }

   public function getHasTermsIdAttribute()
   {
      if (!$this->medical_terms) {
         return null;
      }

      if (empty(json_decode($this->medical_terms))) {
         return null;
      }

      return json_decode($this->medical_terms);
   }

   public function medical_articles()
   {
      return $this->belongsToMany(MedicalArticle::class, 'medical_term_article_link', 'medical_term_id', 'medical_article_id');
   }

   public function brands()
   {
      return $this->belongsToMany(Brand::class, 'brand_terms', 'terminology_id', 'brand_id');
   }

   public function provider_service()
   {
      return $this->hasOne(ProviderService::class, 'services');
   }

   public function provider_services()
   {
      return $this->hasMany(ProviderService::class, 'services');
   }

   public function service()
   {
      return $this->hasOne(Service::class, 'medical_term');
   }

   public function term_connections()
   {
      return $this->hasMany(TermConnectionDescription::class, 'term_id');
   }

   public function term_icons()
   {
      return $this->hasMany(Icon::class, 'term');
   }

   public function user_created()
   {
      return $this->belongsTo(User::class, 'created_by');
   }
}
