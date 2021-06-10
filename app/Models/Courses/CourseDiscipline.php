<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;

class CourseDiscipline extends Model
{
   protected $table = "course_discipline";

   protected $guarded = [];
   
   public function term(){
      return $this->belongsTo(MedicalTerm::class, 'term_id');
   }
}
