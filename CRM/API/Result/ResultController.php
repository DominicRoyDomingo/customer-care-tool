<?php

namespace CRM\API\Result;

use App\Http\Controllers\Controller;
use App\Services\Results\ResultService;
use App\Services\Results\BrandService;
use Illuminate\Http\Request;


class ResultController extends Controller{

	protected $resultserv;
	protected $brandserv;

	function __construct(ResultService $result, BrandService $brand){
		$this->resultserv = $result;
		$this->brandserv = $brand;
	}

	function index(Request $r){

		$result = $this->resultserv->get_all_result([
			'lang' => $r->input('lang')
		]);

		return response()->json($result);

	}


	function add_result(Request $r){

		$formData = array(
            'name' => ucwords($r->postdata)
        );

		$jsonData = string_to_json($r->lang, ['name'], $formData);

        $duplicate = $this->resultserv->check_duplicate([
        	'lang' => $r->lang,
			'postdata' => ucwords($r->postdata)
        ]);

        if($duplicate){
            return response()->json($duplicate);
        }

		$result = $this->resultserv->add_result([
			'lang' => $r->lang,
			'postdata' => $jsonData['name']
		]);

		if($result){

			 $message = ucwords(string_to_value($r->lang, $result->name));

			 return response()->json(alert_success($message, $result));

		}
		
		return response()->json(false);

	}


	function single_result(Request $r){

		$result = $this->resultserv->get_single_result([
			'lang' => $r->lang,
			'id' => $r->id
		]);


		return response()->json(['name' => $result]);

	}

	function update_result(Request $r){
		
		$result = $this->resultserv->update_result([
			'id' => $r->id,
			'lang' => $r->lang,
			'name' => $r->name
		]);

		if($result){

			$message = ucwords(string_to_value($r->lang, $result->name));

            return response()->json(alert_update($message, $result));

		}

	}

	function delete_result(Request $r){

		$result = $this->resultserv->delete_result([
			'id' => $r->id,
			'lang' => $r->lang
		]);

		return response() -> json( alert_delete( $result ) );

	}

	function search_result(Request $request)
    {

        $search = $request->search;
        $lang = $request->input('lang');
        
        $result = $this->resultserv->search_result([
        	'search' => $search,
        	'lang' => $lang
        ]);

        return response()->json($result);
    }

    function get_brands(Request $r){

	      $result = $this->brandserv->get_all_brands([
	      		'current' => $r->current,
	            'limit' => $r->limit,
	            'lang' => $r->lang,
	            'terms' => $r->terms
	      ]);

	      return response()->json($result);

    }

    function update_brand(Request $r){

    	$result = $this->brandserv->updateBrandLink([
    		'result_id' => $r->result_id,
    		'brands' => $r->brands
    	]);

    	return response()->json($result);

    }

    function get_result_brand($result_id){

    	$result = $this->brandserv->single_result_brands([
    		'result_id' => $result_id
    	]);
    	
    	return response()->json($result);

    }

    function orphan_filter(Request $r){

    	$result = $this->resultserv->orphan_filter([
    		'lang' => $r->lang,
    		'name' => $r->name
    	]);

    	return response()->json($result);
    }

}