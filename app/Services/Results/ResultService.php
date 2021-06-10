<?php

namespace App\Services\Results;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Results;
use App\Models\Brand_results;

class ResultService{


	private $result;
    protected $brandres;

	function __construct(Results $result, Brand_results $brandres){
		$this->result = $result;
        $this->brandres = $brandres;
	}

	function get_all_result($request){

		extract($request);

		$query = $this->result::all();

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

        $get_result = $this->result::orderBy('id', 'DESC')->get();

        if($get_result->count()){

	        $result_arr = array();

	        foreach ($get_result as $datas) {
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


	            array_push($result_arr, [

	                'id' => $datas['id'],

	                'name' => $base_name . $notif,

	                'base_name' => $base_name,

	                'created_at' => date('m/d/Y', strtotime($datas['created_at'])),

	                'updated_at' => date('m/d/Y', strtotime($datas['updated_at'])),

	                'convertion' => (!empty($name) ? false : true),

	                'language' => $language

	            ]);
	        }

	        return $result_arr;

	    }

		return false;

	}

	function add_result($request){

		extract($request);

        $query = new Results;
        
        $query->name = $postdata;

        if($query->save()){
        
        	return $query;

        }

        return false;


	}

	function update_result($request){

		extract($request);

        if (!empty($id)) {

            $result_type_check = new Results;

            if ($result_type_check->count() > 0) {

                foreach ($result_type_check->get() as $result) {

                    $result_type_checks_ = $this->view_item_type($result, $lang);

                    if (ucwords($result_type_checks_['name']) == ucwords($name) && $result_type_checks_['id'] != $id) {

                        $message = ucwords(string_to_value($lang, $name));

                        return response()->json(alert_duplicate($message, $formData));
                    }
                }
            }

            $respQuestion = $this->changeData((object) $request);

            return $respQuestion;
        }
	}


    function delete_result($request){

        extract($request);

        $result = $this->result::where('id', $id);
        $brand = $this->brandres::where('result', $id);

        $name = $result->first()->name;

        if ($result->delete()) {

            if($brand->exists()){
                $brand->delete();
            }

            $message = ucwords(string_to_value($lang, $name));

            if(empty($message) || is_null($message)){

                $langs = ['en', 'it', 'de', 'ph-fil', 'ph-bis'];

                foreach($langs as $l){
                    
                    $message = ucwords( string_to_value(  $l, $name ) ); 
                                       
                    if($message != ''){
                        break;
                    }
                }
            }

            return $message;
        }

    }

	function get_single_result($request){

		extract($request);

		$query = $this->result::whereId($id)->first();

        $message = ucwords(string_to_value($lang, $query->name));

        return $message;

	}

    function search_result($request){

        extract($request);

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

        $find = $this->result::where('name', 'like', '%' . $search . '%')->get();

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

            return $find_arr;
        }

        return false;

    }

    function orphan_filter($request){
        extract($request);

        $find = $this->result::orderBy("id", 'desc')->get();        

        if ($find->count()) {
            $find_arr = [];
            $langs = ['en', 'it', 'de', 'ph-fil', 'ph-bis'];

            if($name == 'all'){
                return $this->get_all_result([
                    'lang' => $lang
                ]);
            }

            $notif = $this->get_lang_filter($name);

            foreach ($find as $f) {                
                
                $decode = json_decode($f->name);
                
                $res = $this->view_item_type($f, $name);
                
                if(property_exists($decode, $name)){
                    continue;
                }                

                $base_name = $this->getName($f['id'], $lang);

                if(empty($base_name) || is_null($base_name)){

                    foreach ($langs as $l) {
                        if($l == $name){
                            continue;
                        }            

                        $base_name = $this->getName($f['id'], $l);
                        if ($base_name != '') {
                            break;
                        }
                    }
                }
                

                if($base_name != ''){
                    $res['name'] = $base_name . $notif;
                    $res['base_name'] = $base_name;
                    $res['created_at'] = date("m/d/Y", strtotime($res['created_at']));
                    $res['updated_at'] = date("m/d/Y", strtotime($res['updated_at']));
                    $find_arr[] =  $res;
                }
                
            }

            return $find_arr;
        }

    }

    function check_duplicate($request){

        extract($request);

        $result_check = new Results;
        $duplicate = false;

        if ($result_check->count() > 0) {

            foreach ($result_check->get() as $result) {

                $course_type_checks_ = $this->view_item_type($result, $lang);

                if (ucwords($course_type_checks_['name']) == ucwords($postdata)) {

                    $message = ucwords(string_to_value($lang, $postdata));

                    $duplicate = alert_duplicate($postdata, [ 'name' => $postdata ]);

                    break;
                }
            }
        }

        return $duplicate;

    }

    public function get_lang_filter($lang){
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

        return " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
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

        $tags = $this->result::whereId($id)->first();

        $name = ucwords(string_to_value($lang, $tags->name));

        return $name;
    }

    public function changeData($data)
    {


        $lang = $data->lang;

        $type = $this->result::whereId($data->id)->first();

        $typeVal = string_add_json($lang, ucwords($data->name), string_remove($lang, $type->name));

        $type->name = $typeVal;

        $type->save();

        return $type;
    }
}