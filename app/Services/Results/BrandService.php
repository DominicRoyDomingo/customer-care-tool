<?php

namespace App\Services\Results;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Results;
use App\Models\Brand;
use App\Models\Brand_results;

class BrandService{


	protected $branddb;
	protected $brandres;

	function __construct(Brand $brand, Brand_results $brandres){
		$this->branddb = $brand;
		$this->brandres = $brandres;
	}


	function get_all_brands($request){

		 extract($request);

		 $count = $this->branddb::count();
		 
		 $result_1 = [];
		 $result_2 = [];

		 if(count($terms)){
		 	$query_terms = $this->branddb::whereIn('id', $terms)->get();
		 	if($query_terms->count()){
		 		$result_1 = $this->getResult($query_terms, $current, $lang, true);
		 	}
		 }
		 
		 if(count($terms)){
		 	$query = $this->branddb::whereNotIn('id', $terms)->get();
		 }
		 else{
		 	$query = $this->branddb::all();
		 }
	     
	      
	     if($query->count()){

	      	$result_2 = $this->getResult($query, $current, $lang, false);

	      	$result = array_merge($result_1, $result_2);

	        $obj = [
	          'total' => $count,
	          'query' => $result
	        ];

	        return $obj;
	      }
	      
	      return false;


	}

	function updateBrandLink($request){

		extract($request);

		$find = $this->brandres::where('result', $result_id);
		
		$is_update = false;

		if($find->exists()){
			$find->delete();	
		}
		
		$insert = array_map(function($item) use ($result_id){
			return [
				'result' => $result_id,
				'brand' => $item
			];
		}, $brands);

		$insert = $this->brandres::insert($insert);

		return $insert;
	}

	function single_result_brands($request){

		extract($request);

		$find = $this->brandres::where('result', $result_id)->get();

		if($find->count()){

			$brands = [];

			foreach($find as $f){
				$brands[] = $f->brand;
			}

			return [
				'brands' => json_encode($brands)
			];
			
		}

		return false;

	}

	function searchTerms($request){

		 extract($request);

		 if(empty($search)){
		 	return $this->get_all_brands($request);
		 }

		 $result_1 = [];
		 $result_2 = [];

		 if(count($terms)){
		 	$query = $this->branddb::whereIn('id', $terms)->where('name', 'like', '%' . $search . '%')->offset($current)->limit($limit)->get();

		 	if($query->count()){
		 		$result_1 = $this->getResult($query, $current, $lang, false);
		 	}
		 }
		 
		 if(count($terms)){
		 	$query = $this->branddb::whereNotIn('id', $terms)->where('name', 'like', '%' . $search . '%')->offset($current)->limit($limit)->get();
		 }
		 else{
		 	$query = $this->branddb::where('name', 'like', '%' . $search . '%')->offset($current)->limit($limit)->get();
		 }

		 if($query->count()){

	      	$result_2 = $this->getResult($query, $current, $lang, false);

	      	$result = array_merge($result_1, $result_2);

	        $obj = [
	          'total' => count($result),
	          'query' => array_splice( $result, 0, $limit )
	        ];

	        return $obj;
	     }

	     return false;

	}

	function getResult($query, &$current, $lang, $is_linked){

		$query_arr = [];

         foreach($query as $q){

	          $query_arr[] = [
	            'index' => $current,
	            'id' => $q->id,
	            'name' => $q->name,
	            'is_linked' => $is_linked
	          ];

	          $current++;
	 	}

	 	return $query_arr;
	}

}
