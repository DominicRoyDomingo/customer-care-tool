<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Model;
use App\Models\MedicalStuff\MedicalTerm;

class CourseReciepient extends Model
{
   protected $table = "course_recepient";

   protected $guarded = [];

   public function term(){
      return $this->belongsTo(MedicalTerm::class, 'term_id');
   }
}
