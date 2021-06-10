<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Model;
use App\Models\Courses\CourseType;

class CourseInfo extends Model
{
   protected $table = "course_info";

   protected $guarded = [];

   protected $appends = [
      'has_course_types'
   ];

   public function getHasCourseTypesAttribute()
   {
      $courseTypeId = json_decode($this->course_types) ?: [];

      return collect($courseTypeId)
         ->map(
            function ($id) {
               return CourseType::find($id);
            }
         )
         ->values();
   }
   
}
