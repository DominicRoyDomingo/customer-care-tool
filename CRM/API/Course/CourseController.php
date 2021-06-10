<?php


namespace CRM\API\Course;

use App\Helpers\Algolia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Courses\CourseType;
use App\Models\LanguageValidator;
use Illuminate\Support\Facades\DB;
use App\Models\Courses\CourseInfo;
use App\Models\ItemType;

class CourseController extends Controller
{

    private $model;

    public function __construct(CourseInfo $model)
    {
        $this->model = $model;
    }

    public function index()
    {
    }

    public function get_course_type(Request $r)
    {

        $all = CourseType::all();

        $lang = $r->input('lang');

        $language = 'English';

        if ($lang == 'it') {
            $language = 'Italian';
        }
        if ($lang == 'de') {
            $language = 'German';
        }
        if ($lang == 'ph-fil') {
            $language = 'Filipino';
        }
        if ($lang == 'ph-bis') {
            $language = 'Visayan';
        }


        $get_course_type = CourseType::orderBy( 'id', 'desc' ) -> get();


        $course_type = array();

        foreach ($get_course_type as $datas) {
            $base_name = $this->getName($datas['id'], $lang);
            $notif = '';
            if ($base_name == '') {
                $langs = ['it', 'de', 'ph-fil', 'ph-bis'];
                $notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
                $base_name = $this->getName($datas['id'], 'en');
                if ($base_name == '') {
                    foreach ($langs as $l) {
                        $base_name = $this->getName($datas['id'], $l);
                        if ($base_name != '') {
                            break;
                        }
                    }
                }
            }


            array_push($course_type, [

                'id' => $datas['id'],

                'name' => $base_name . $notif,

                'base_name' => $base_name,

                'created_at' => date('m/d/Y', strtotime($datas['created_at'])),

                'updated_at' => date('m/d/Y', strtotime($datas['updated_at'])),

                'convertion' => (!empty($name) ? false : true),

                'language' => $language

            ]);
        }

        return response()->json($course_type);
    }

    public function add_course_type(Request $r)
    {

        $lang = $r->input('lang');

        $type = $r->course_type;

        $course_check = new CourseType;

        if ($course_check->count() > 0) {

            foreach ($course_check->get() as $course) {

                $course_type_checks_ = $this->view_item_type($course, $lang);

                if (ucwords($course_type_checks_['name']) == ucwords($r->course_type)) {

                    $message = ucwords(string_to_value($lang, $r->course_type));

                    return response() -> json( alert_duplicate( $message, $r -> course_type) );
                }
            }
        }

        $formData = array(

            'course_type' => ucwords($r->course_type)

        );

        $jsonData = string_to_json($lang, ['course_type'], $formData);

        $course = new CourseType;
        $course->name = $jsonData['course_type'];

        if ($course->save()) {

            $message = ucwords(string_to_value($lang, $course->name));

            return response()->json(alert_success($message, $course));
        }
    }

    public function getCourseTypeName(Request $request, $id, $lang)
    {

        $course_type = CourseType::whereId($id)->first();

        $message = ucwords(string_to_value($lang, $course_type->name));

        return response()->json(['name' => $message]);
    }

    public function updateCourseType(Request $request)
    {

        $formData = json_decode($request->input('data'));

        if (!empty($formData->id)) {

            $lang = $formData->language;

            $course_type_check = new CourseType;

            if ($course_type_check->count() > 0) {

                foreach ($course_type_check->get() as $course) {

                    $course_type_checks_ = $this->view_item_type($course, $lang);

                    if (ucwords($course_type_checks_['name']) == ucwords($formData->name) && $course_type_checks_['id'] != $formData->id) {

                        $message = ucwords(string_to_value($lang, $formData->name));

                        return response()->json(alert_duplicate($message, $formData));
                    }
                }
            }

            $respQuestion = $this->changeData($request);

            if ($respQuestion) {

                $message = ucwords(string_to_value($lang, $respQuestion->name));

                return response()->json(alert_update($message, $respQuestion));
            }
        }
    }

    public function deleteCourseType(Request $request, $id)
    {

        $lang = $request->input('lang');

        $course_type = CourseType::whereId($id);

        $name = $course_type->first()->name;


        if ($course_type->delete()) {

            $message = ucwords(string_to_value($lang, $name));

            return response()->json(alert_delete($message));
        }
    }

    public function searchCourseType(Request $request)
    {

        $search = $request->search;
        $lang = $request->input('lang');
        $language = 'English';

        if ($lang == 'it') {
            $language = 'Italian';
        }
        if ($lang == 'de') {
            $language = 'German';
        }
        if ($lang == 'ph-fil') {
            $language = 'Filipino';
        }
        if ($lang == 'ph-bis') {
            $language = 'Visayan';
        }

        $find = CourseType::where('name', 'like', '%' . $search . '%')->get();

        if ($find->count()) {
            $find_arr = [];
            foreach ($find as $f) {
                $res = $this->view_item_type($f, $lang);
                $base_name = $this->getName($f['id'], $lang);
                $notif = '';
                if ($base_name == '') {
                    $langs = ['it', 'de', 'ph-fil', 'ph-bis'];
                    $notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
                    $base_name = $this->getName($f['id'], 'en');
                    if ($base_name == '') {
                        foreach ($langs as $l) {
                            $base_name = $this->getName($f['id'], $l);
                            if ($base_name != '') {
                                break;
                            }
                        }
                    }
                }

                $res['name'] = $base_name . $notif;
                $res['base_name'] = $base_name;
                $res['created_at'] = date("m/d/Y", strtotime($res['created_at']));
                $res['updated_at'] = date("m/d/Y", strtotime($res['updated_at']));
                $find_arr[] =  $res;
            }

            return response()->json($find_arr);
        }

        return response()->json(false);
    }

    public function sortCourseType(Request $request)
    {

        $sortcol = $request->sortcol;
        $search = $request->filter;
        $sort = ($request->sort == true) ? 'desc' : 'asc';
        $lang = $request->input('lang');

        $find = CourseType::where('name', 'like', '%' . $search . '%')->get();

        if ($sortcol == 'index') {
            $find = CourseType::where('name', 'like', '%' . $search . '%')->orderBy('id', $sort)->get();
        } else if ($sortcol == 'term_type_name') {
            $find = CourseType::where('name', 'like', '%' . $search . '%')->orderBy('type_name', $sort)->get();
        } else if ($sortcol == 'created_at') {
            $find = CourseType::where('name', 'like', '%' . $search . '%')->orderBy('created_at', $sort)->get();
        }

        $language = 'English';
        if ($lang == 'it') {
            $language = 'Italian';
        }
        if ($lang == 'de') {
            $language = 'German';
        }
        if ($lang == 'ph-fil') {
            $language = 'Filipino';
        }
        if ($lang == 'ph-bis') {
            $language = 'Visayan';
        }

        if ($find->count()) {
            $find_arr = [];
            foreach ($find as $f) {
                $base_name = $this->getName($f['id'], $lang);
                $res = $this->view_item_type($f, $lang);
                $notif = '';
                if ($base_name == '') {
                    $langs = ['it', 'de', 'ph-fil', 'ph-bis'];
                    $notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
                    $base_name = $this->getName($f['id'], 'en');
                    if ($base_name == '') {
                        foreach ($langs as $l) {
                            $base_name = $this->getName($f['id'], $l);
                            if ($base_name != '') {
                                break;
                            }
                        }
                    }
                }

                $res['name'] = $base_name . $notif;
                $res['base_name'] = $base_name;
                $res['created_at'] = date("m/d/Y", strtotime($res['created_at']));
                $res['updated_at'] = date("m/d/Y", strtotime($res['updated_at']));
                $find_arr[] =  $res;
            }


            return response()->json($find_arr);
        }

        return response()->json(false);
    }

    public function view_item_type($data, $lang)
    {

        return [

            'id' => $data->id,

            'name' => string_to_value($lang, $data->name),

            'created_at' => $data->created_at,

            'updated_at' => $data->updated_at,

        ];
    }

    public function getName($id, $lang)
    {

        $tags = CourseType::whereId($id)->first();

        $name = ucwords(string_to_value($lang, $tags->name));

        return $name;
    }

    public function changeData($data)
    {
        $formData = json_decode($data->input('data'));

        $lang = $formData->language;

        $type = CourseType::whereId($formData->id)->first();

        $typeVal = string_add_json($lang, ucwords($formData->name), string_remove($lang, $type->name));

        $type->name = $typeVal;

        $type->save();

        return $type;
    }

    public function syncToAlgolia(Request $request){
        $data = Algolia::update_articletype_syncreference(27, $request->brand_id);
        
        return response()->json($data);
    } 
}
