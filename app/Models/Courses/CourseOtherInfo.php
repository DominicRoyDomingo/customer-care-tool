<?php

namespace App\Models\Courses;

use App\Models\Courses\Traits\Attributes\CourseTypeAttribute;
use App\Models\InformationType;
use Illuminate\Database\Eloquent\Model;

class CourseOtherInfo extends Model
{
    use CourseTypeAttribute;

    protected $table = 'course_information_types';

    protected $guarded = [];

    protected $appends = [
        // 'base_name',
        // 'course_type_name',
        'is_loading',
        // 'is_english',
        // 'is_italian',
        // 'is_german',
        // 'is_bisaya',
        // 'is_filipino',
        // 'base_language',
    ];

    /**
     * @return bool
     */
    public function getIsLoadingAttribute()
    {
        return false;
    }

    // public function getBaseNameAttribute()
    // {
    //     return $this->get_base_name();
    // }

    // public function getCourseTypeNameAttribute()
    // {
    //     return $this->get_base_name(true);
    // }

    public function information_type()
    {
        return $this->belongsTo(InformationType::class, 'information_type_id');
    }
}
