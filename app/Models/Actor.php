<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jobs\JobDescription;
use App\Models\Jobs\JobProfession;
use App\Models\Actors\ActorProfession;
use App\Models\MedicalStuff\MedicalTerm;

class Actor extends Model
{
   protected $table = 'actors';
   protected $guarded = [];

   protected $appends = [
      'full_name',
      'physical_code_types',
      'specializations',
      'other_infos',
      'selected_brands',
      'medical_terms',
   ];


   public function scopeActive($query)
   {
      return $query
         ->join('actors_brand', function ($join) {
            $join
               ->on('actors_brand.actor', '=', 'actors.id')
               ->where('actors_brand.brands', request()->brand_id);
         });
   }

   public function actors_brand_item()
   {
      return $this->hasOne(ActorsBrand::class, 'actor', 'id');
   }

   public function actor_specialization()
   {
      return $this->hasMany(ActorSpecialization::class, 'actor', 'id');
   }

   public function actor_field_of_professions()
   {
      return $this->hasMany(ActorProfession::class, 'actor', 'id');
   }

   public function actor_field_of_professions_items()
    {
      return $this->belongsToMany(MedicalTerm::class, 'actor_professions', 'actor', 'profession');
    }

   public function actor_terms()
   {
      return $this->hasMany(ActorTerm::class, 'actor', 'id');
   }

   public function actor_terms_items()
   {
      return $this->belongsToMany(MedicalTerm::class, 'actor_terms', 'actor', 'term');
   }

   public function getFullNameAttribute()
   {
      if ($this->firstname == "") {
         return $this->surname;
      }

      $name =  $this->surname . ", " . $this->firstname;
      if ($this->middlename != "")
         $name .= " " . ucfirst(substr($this->middlename, 0, 1)) . ".";
      return $name;
   }

   public function getMedicalTermsAttribute()
   {
      return $this->actor_terms->pluck('term');
   }

   public function getSelectedBrandsAttribute()
   {

      $brands = ActorsBrand::where('actor', $this->id)->get();
      $brandsArr = array();
      foreach ($brands as $brand) {
         array_push($brandsArr, $brand->brands);
      }
      return $brandsArr;
   }

   public function getPhysicalCodeTypesAttribute()
   {
      if ($this->physical_code == null) return null;
      $array = json_decode($this->physical_code, true);

      $array = collect($array)->map(function ($actor) {
         $actor['physical_code_type'] = PhysicalCodeType::where('id', $actor['physical_code_type'])->get();
         return $actor;
      });

      return $array;
   }

   public function getSpecializationsAttribute()
   {
      if ($this->specialization == null) return null;
      $array = json_decode($this->specialization, true);

      $array = collect($array)->map(function ($actor) {
         $actor['specialization'] = JobDescription::where('id', $actor['specialization'])->get();
         $actor['profession'] = JobProfession::where('id', $actor['profession'])->get();
         return $actor;
      });

      return $array;
   }

   public function getOtherInfosAttribute()
   {
      if ($this->other_info == null) return null;
      $array = json_decode($this->other_info, true);

      $array = collect($array)->map(function ($other_info) {
         $other_info['type'] = InformationType::where('id', $other_info['type'])->get();
         return $other_info;
      });

      return $array;
   }
}
