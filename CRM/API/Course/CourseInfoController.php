<?php


namespace CRM\API\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Courses\CourseInfo;

class CourseInfoController extends Controller
{

    private $model;

    public function __construct(CourseInfo $model)
    {
        $this->model = $model;
    }

    public function store(Request $request)
    {
    }
}
