<?php

namespace App\Services\Questions\Terms;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\MedicalStuff\MedicalTerm;

class LinkTerms{
	
	private $questiondb;
	private $medicaldb;

	function __construct(Question $question, MedicalTerm $medical){
		$this->questiondb = $question;
		$this->medicaldb = $medical;
	}


	function getAllTerms($request){

		 extract($request);

		 $count = $this->medicaldb::count();
		 
		 $result_1 = [];
		 $result_2 = [];

		 if(count($terms)){
		 	$query_terms = $this->medicaldb::whereIn('id', $terms)->offset($current)->limit($limit)->get();
		 	if($query_terms->count()){
		 		$result_1 = $this->getResult($query_terms, $current, $lang, true);
		 	}
		 }
		 
		 if(count($terms)){
		 	$query = $this->medicaldb::whereNotIn('id', $terms)->offset($current)->limit($limit)->get();
		 }
		 else{
		 	$query = $this->medicaldb::offset($current)->limit($limit)->get();
		 }
	     
	      
	     if($query->count()){

	      	$result_2 = $this->getResult($query, $current, $lang, false);

	      	$result = array_merge($result_1, $result_2);

	        $obj = [
	          'total' => $count,
	          'query' => array_splice( $result, 0, $limit )
	        ];

	        return $obj;
	      }
	      
	      return false;

	}

	function updateQuestionTerms($request){

		extract($request);

		$find = $this->questiondb::where('id', $id);

		$is_update = false;

		if($find->exists()){
			$row = $find->first();
			$terms = (array) json_decode($row->terms);
			if(!in_array($term->id, $terms)){
				array_push($terms, $term->id);
			}
			else
			{
				$terms = array_filter($terms, function($item) use ($term){
					if($item != $term->id){
						return $item;
					}
				});

				if(count($terms)){
					$temp_arr = [];
					foreach($terms as $t){
						$temp_arr[] = $t;
					}

					$terms = $temp_arr;
				}
				
			}


			$is_update = $find->update([
				'terms' => (array) $terms
			]);
		}

		return $term->id;
	}

	public function singleQuestionTerms($request){

		extract($request);

		$find = $this->questiondb::where('id', $id)->get();
		if($find->count()){
			$row = $find->first();
			if(!empty($row->terms)){

				$obj = [
					'terms' => $row->terms
				];

				return $obj;
			}
			
		}

		return false;

	}

	public function searchTerms($request){

		 extract($request);

		 if(empty($search)){
		 	return $this->getAllTerms($request);
		 }

		 $result_1 = [];
		 $result_2 = [];

		 if(count($terms)){
		 	$query = $this->medicaldb::whereIn('id', $terms)->where('name', 'like', '%' . $search . '%')->offset($current)->limit($limit)->get();

		 	if($query->count()){
		 		$result_1 = $this->getResult($query, $current, $lang, false);
		 	}
		 }
		 
		 if(count($terms)){
		 	$query = $this->medicaldb::whereNotIn('id', $terms)->where('name', 'like', '%' . $search . '%')->offset($current)->limit($limit)->get();
		 }
		 else{
		 	$query = $this->medicaldb::where('name', 'like', '%' . $search . '%')->offset($current)->limit($limit)->get();
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


	public function getName($id, $lang)
	  {

	      $tags = $this->medicaldb::whereId($id)->first();

	      $name = ucwords(string_to_value($lang, $tags->name));

	      return $name;
	  }


	public function getResult($query, &$current, $lang, $is_linked){

		$query_arr = [];

		$langs = ['it', 'de', 'ph-fil', 'ph-bis'];
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

         foreach($query as $q){
			 $name = $this->getName($q->id, $lang);
			 $notif = '';
	         if($name == ''){
	         	$notif = " <i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
	            foreach ($langs as $l) {
	                $name = $this->getName($q->id, $l);
	                if ($name != '') {
	                    break;
	                }
	            }
	          } 

	          $query_arr[] = [
	            'index' => $current,
	            'id' => $q->id,
	            'name' => $name . ' ' . $notif,
	            'base_name' => $name,
	            'is_linked' => $is_linked
	          ];

	          $current++;
	 	}

	 	return $query_arr;
	}

}



