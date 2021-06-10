<?php

namespace App\Models;

use App\Models\Courses\Traits\Attributes\CourseTypeAttribute;
use Illuminate\Database\Eloquent\Model;


class Results extends Model
{

	protected $table = "results";
	protected $guarded = [];
	
}