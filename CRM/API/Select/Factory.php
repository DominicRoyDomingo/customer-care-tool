<?php

namespace CRM\API\Select;

use App\Models\Actor;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
use App\Models\Brand;
use App\Models\Courses\CourseType;
use App\Models\InformationType;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Jobs\JobCategory;
use App\Models\Jobs\JobProfession;
use App\Models\Jobs\JobDescription;
use App\Models\Mails\Campaign;
use App\Models\Profile;
use App\Models\MedicalStuff\MedicalStructureType;
use App\Models\MedicalStuff\MedicalTerm;
use App\Models\MedicalStuff\TypeDate;
use App\Models\Questions\Question;
use App\Models\PaymentPlan\PaymentPlan;
use App\Models\StatisticsTable;

class Factory
{
   public static function make($type, $scope)
   {
      $select = static::getSelect($type);

      if (!$select) {
         throw new \Exception("No select type for {$type}.");
      }

      if (!in_array($scope, $select['scope'])) {
         throw new \Exception("Select `{$type}` is not available for `{$scope}`.");
      }

      if ($select['query'] instanceof DeferredSelectInterface) {
         static::validateDeferredRules($select['query']);
         return $select['query']->deferred();
      }

      return $select['query'];
   }

   protected static function validateDeferredRules($deferred)
   {
      if (method_exists($deferred, 'rules')) {
         request()->validate($deferred->rules());
      }
   }

   public static function getSelect($type)
   {
      return Arr::get(static::list(), $type);
   }

   public static function list()
   {
      return [
         'companies' => [
            'query' => Brand::whereNotNull('name')->orderBy('name'),
            'scope' => ['all'],
         ],
         'job_category' => [
            'query' => JobCategory::whereNotNull('category')->orderBy('category'),
            'scope' => ['all'],
         ],
         'job_profession' => [
            'query' => JobProfession::whereNotNull('profession')->orderBy('profession'),
            'scope' => ['all'],
         ],
         'job_description' => [
            'query' => JobDescription::whereNotNull('description')->orderBy('description'),
            'scope' => ['all', 'list'],
         ],
         'users' => [
            'query' => User::whereNotNull('email')->orderBy('last_name'),
            'scope' => ['all'],
         ],
         'profiles' => [
            'query' => Profile::whereNotNull('primary_email')->orderBy('surname'),
            'scope' => ['all'],
         ],
         'brands' => [
            'query' => Brand::whereNotNull('name')->orderBy('name'),
            'scope' => ['all'],
         ],
         'campaigns' => [
            'query' => Campaign::whereNotNull('campaign')->orderBy('campaign'),
            'scope' => ['all'],
         ],
         'statistics_table' => [
            'query' => StatisticsTable::whereNotNull('name')->orderBy('name'),
            'scope' => ['all'],
         ],
         'terms' => [
            'query' => MedicalTerm::select('medical_terms.*')->whereNotNull('name')->active()->orderBy("name"),
            'scope' => ['all', 'list'],
         ],
         'actors' => [
            'query' => Actor::select('actors.*')->whereNotNull('surname')->active()->orderBy("surname"),
            'scope' => ['all'],
         ],
         'item_types' => [
            'query' => ItemType::whereNotNull('type_name')->orderBy("type_name"),
            'scope' => ['all'],
         ],
         'payment_plans' => [
            'query' => PaymentPlan::whereNotNull('name')->orderBy("name"),
            'scope' => ['all'],
         ],
         'date_types' => [
            'query' => TypeDate::whereNotNull('name')->orderBy("name"),
            'scope' => ['all'],
         ],
         'course_type' => [
            'query' => CourseType::whereNotNull('name')->orderBy("name"),
            'scope' => ['all', 'list'],
         ],
         'information_type' => [
            'query' => InformationType::whereNotNull('name')->orderBy("name"),
            'scope' => ['all'],
         ],
         'questions' => [
            'query' => Question::whereNotNull('question')->orderBy("question"),
            'scope' => ['all'],
         ]
      ];
   }

   public static function getDefaults($type)
   {
      $select = static::getSelect($type);
      return value($select['defaults'] ?? []) ?? [];
   }
}
