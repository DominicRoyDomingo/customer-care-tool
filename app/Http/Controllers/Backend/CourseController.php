<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\ItemType;

use App\Models\LanguageValidator;

use Illuminate\Support\Facades\DB;


class CourseController extends Controller{


	public function course_type(){
		return view('backend.pages.courses.course-type');
	}

}