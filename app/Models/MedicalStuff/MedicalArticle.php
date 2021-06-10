<?php

namespace App\Models\MedicalStuff;

use DB;
use App\Models\Actor;
use App\Models\Brand;
use App\Models\ArticleContentMaker\Geolocalization;
use App\Models\ArticleContentMaker\GeolocalizationTemplate;
use App\Models\Courses\CourseDiscipline;
use App\Models\Courses\CourseInfo;
use App\Models\Courses\CourseInformationType;
use App\Models\Courses\CourseOtherInfo;
use App\Models\Courses\CourseReciepient;
use App\Models\ItemType;
use App\Models\SyncReference;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;
use App\Models\MedicalStuff\Traits\ArticleAttributeTrait;
use App\Models\MedicalStuff\Traits\TermTraits;
use App\Models\MedicalStuff\TypeDate;
use App\Models\MedicalStuff\PublishingItemContent;
use App\Models\MedicalStuff\ArticleAuthor;
use App\Models\MedicalStuff\Traits\AlgoliaTrait;
use App\Models\MedicalStuff\Traits\Attributes\ArticleAttribute;
use App\Models\MedicalStuff\Traits\Methods\ArticleMethods;
use App\Models\MedicalStuff\Traits\Methods\TermMethods;
use App\Models\MedicalStuff\Traits\Scope\ArticleScope;
use Laravel\Scout\Searchable;

class MedicalArticle extends Model
{
   use TermTraits,
      ArticleMethods,
      TermMethods,
      ArticleAttributeTrait,
      ArticleScope,
      ArticleAttribute,
      AlgoliaTrait;

   use Searchable;

   protected $guarded = [];


   private $courses = ['courses', 'course', 'corsi', 'corso', 'kurs'];

   protected $appends = [
      'is_loading',
      'is_english',
      'is_italian',
      'is_german',
      'is_bisaya',
      'is_filipino',
      'italian_link',
      'german_link',
      'english_link',
      'base_name',
      'base_link',
      'has_authors',
      'article_title',
      'date_created',
      'date_modified',
      'base_language',
      'route_show',
      'bisaya_link',
      'filipino_link',
      'has_translation',
      'is_linked_to_term',
      'has_course_types'
   ];

   public function searchableAs()
   {
      $isStaging = request()->getHttpHost() == 'stagingcct.med4care.online';
      $type = request()->algolia_syncType ?? null;
      $config = config('scout.prefix');

      if ($type) {
         return ($isStaging) ? $config . $type . '_staging' : $config . $type;
      } else {
         return ($isStaging) ? $config . 'geolocalization_staging' : $config . 'geolocalization';
      }
   }

   public function toSearchableArray()
   {
      $type = request()->algolia_syncType ?? null;

      if ($type) {
         switch (strtolower($type)) {
            case 'course':
               return $this->setSearchableCourseArray();
               break;
         }
      } else {
         return $this->setSearchableGeolocalizedArray();
      }
   }

   public function getIsLoadingAttribute()
   {
      return false;
   }

   public function getRouteShowAttribute()
   {
      return route("admin.software-publishing.item-show", $this->id);
   }

   public function getIsLinkedToTermAttribute()
   {
      if (!request()->term_id) {
         return false;
      }

      $termId = request()->term_id;

      $term = MedicalTerm::findOrFail($termId);

      foreach ($term->medical_articles as $article) {
         if ($article->id === $this->id) {
            return true;
         }
      }

      return false;
   }

   public function getHasTranslationAttribute()
   {
      $baseName = $this->base_name();

      if (!$baseName) {
         return false;
      }

      return true;
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


   public function getBaseLinkAttribute()
   {
      $baseLink = $this->english_link;

      if (locale() === 'it')  $baseLink = $this->italian_link;
      if (locale() === 'de')  $baseLink = $this->german_link;
      if (locale() === 'ph-fil')  $baseLink = $this->filipino_link;
      if (locale() === 'ph-bis')  $baseLink = $this->bisaya_link;

      if (!$baseLink) {
         $priorityBaseLink = $this->english ?? $this->italian_link;
         $baseLink .= $priorityBaseLink ?? $this->german_link;
      }

      return $baseLink;
   }

   public function getBaseNameAttribute()
   {
      return $this->get_base_name();
   }

   public function getArticleTitleAttribute()
   {
      return $this->get_base_name(true);
   }

   public function getDateCreatedAttribute()
   {
      return $this->created_at->format('F d, Y');
   }

   public function getDateModifiedAttribute()
   {
      return $this->updated_at->format('F d, Y');
   }

   public function getHasCourseTypesAttribute()
   {
      foreach ($this->publishing_item_type_articles as $type) {
         if (in_array(strtolower($type->base_name), $this->courses)) {
            return $type;
         }
      }
      return null;
   }


   public function getHasAuthorsAttribute()
   {
      if (!$this->authors) {
         return null;
      }
      $items = [];

      foreach (string_to_value('actors', $this->authors) as $id) {
         $items[] = Actor::find($id);
      }

      return $items;
   }

   public function medical_terms()
   {
      return $this->belongsToMany(MedicalTerm::class, 'medical_term_article_link', 'medical_article_id', 'medical_term_id');
   }

   public function publishing_item_type_articles()
   {
      return $this->belongsToMany(ItemType::class, 'publishing_item_type_link', 'article_id', 'publishing_item_type_id');
   }

   public function brands()
   {
      return $this->belongsToMany(Brand::class, 'brand_articles', 'article_id', 'brand_id');
   }

   public function geolocalizations()
   {
      return $this->hasMany(Geolocalization::class, 'article', 'id');
   }

   public function geolocalization_template()
   {
      return $this->hasOne(PublishingItemContent::class, 'publishing_item', 'id');
   }

   public function type_dates()
   {
      return $this->belongsToMany(TypeDate::class, 'article_dates', 'article_id', 'type_date_id')
         ->withPivot('article_date');
   }

   public function author_slot()
   {
      return $this->hasMany(ArticleAuthor::class, 'article');
   }

   public function image_slot()
   {
      return $this->hasMany(ArticleImage::class, 'article');
   }

   public function publishing_item_content()
   {
      return $this->hasOne(PublishingItemContent::class, 'publishing_item');
   }

   public function course_info()
   {
      return $this->hasOne(CourseInfo::class, 'article_id');
   }

   public function course_information_types()
   {
      return $this->hasMany(CourseOtherInfo::class, 'article_id');
   }

   public function article_authors()
   {
      return $this->hasOne(ArticleAuthor::class, 'article');
   }

   public function geolocalization_sync_reference()
   {
      return $this->hasOne(SyncReference::class, 'table_id', 'id')->where('source_table', 'geolocalizations');
   }

   public function course_sync_reference()
   {
      return $this->hasOne(SyncReference::class, 'table_id', 'id')->where('sync_class', 'Course');
   }

   public function course_term_descipline()
   {
      return $this->belongsToMany(MedicalTerm::class, 'course_discipline', 'article_id', 'term_id');
   }

   public function course_term_recepient()
   {
      return $this->belongsToMany(MedicalTerm::class, 'course_recepient', 'article_id', 'term_id');
   }
}
