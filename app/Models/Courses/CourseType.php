<?php

namespace App\Models\Courses;

use App\Models\Courses\Traits\Attributes\CourseTypeAttribute;
use Illuminate\Database\Eloquent\Model;

class CourseType extends Model
{
    use CourseTypeAttribute;

    protected $guarded = [];

    protected $appends = [
        'base_name',
        'course_type_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'base_language',
    ];

    /**
     * @return bool
     */
    public function getIsLoadingAttribute()
    {
        return false;
    }

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getCourseTypeNameAttribute()
    {
        return $this->get_base_name(true);
    }
}
