<?php

namespace CRM\API\Reasons;

use App\Http\Controllers\Controller;
use App\Models\Reasons;
use App\Models\Brand;
use App\Models\RequestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReasonsController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;

      $filter_select = $request -> filter_select;

      $keyword = $request -> search;

      $reasons_connection = Reasons::select( [ 'id', 'brands' ] ) 

      -> when( $keyword, function( $query ) use ( $keyword ) {

         if( ! empty( $keyword ) ) {

            $new_like_query = '(

               ( name LIKE "%' . ucwords( $keyword ) . '%" )

               OR

               ( abbreviation LIKE "%' .ucwords( $keyword ) . '%" )

               OR 

               ( SELECT name AS request_type_name FROM request_type WHERE id = request_type ) LIKE "%' . ucwords( $keyword ) . '%"

               )';

               return $query -> whereRaw( " ( " .$new_like_query . ")" );

            }

         } )

         -> where( 'org_id', '=', $request -> org_id ) 

         -> get();

         if (!$request->sortbyLang || $request->sortbyLang === 'all') {
            $reasons_connection = $reasons_connection;
         } else {
            $lang = $request->sortbyLang;
            $reasons_connection = $reasons_connection
            ->filter(function ($term) {
               if (!$term->has_translation) return $term;
            })
            ->values();
         }

         $array_ids = [];

         foreach( $reasons_connection as $get_brands ) {

            if( in_array( $request -> brand, json_decode( $get_brands -> brands ) ) == true ) {

               $array_ids[] = $get_brands -> id;

            }

         }

         $language = 'English';

         if( $lang == 'it') { $language = 'Italian'; } 
         if( $lang == 'de' ) { $language = 'German'; } 
         if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
         if( $lang == 'ph-bis' ) { $language = 'Visayan'; }
         
         $reasons = Reasons::whereIn( 'id', $array_ids ) -> get();
   
         $reasonsArray = array();

         foreach( $reasons as $datas ) {
         
            $name = $this -> getNameReasons( $datas[ 'id' ], $lang );

            $base_name = ( ! empty( $name ) ? $name : $this -> getNameReasons( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

               $base_name_italy = $this -> getNameReasons( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                  $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameReasons( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameReasons( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameReasons( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }

            }

            $request_type = $this -> getNameRequestType( $datas[ 'request_type' ], $lang );

            $base_request_type = ( ! empty( $request_type ) ? $request_type : $this -> getNameRequestType( $datas[ 'request_type' ], 'en' ) );
            
            if( $request_type == '' && $base_request_type == '' ) {

               $request_base_name_italy = $this -> getNameRequestType( $datas[ 'request_type' ], 'it' );
              
               if( $request_base_name_italy !== '' ) {

                  $base_request_type = $request_base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameRequestType( $datas[ 'request_type' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_request_type = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameRequestType( $datas[ 'request_type' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_request_type = $base_name_filipino;
   
                     }

                     else{

                        $base_request_type = $this -> getNameRequestType( $datas[ 'request_type' ], 'ph-bis' );
                        
                     }

                  }

                }
               
            }

            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               
               array_push( $reasonsArray, [

                  'id' => $datas[ 'id' ],

                  'name' => $base_name,

                  'abbreviation' => ( $datas['abbreviation'] !== 'null' ? $datas['abbreviation'] : '' ),

                  'description' => $datas['description'],

                  'request_type' => $datas['request_type'],

                  'request_type_name' => $base_request_type,

                  'brands' => $datas['brands'],

                  'org_id' => $datas['org_id'],

                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'convertion_request_type' => ( ! empty( $request_type ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {
                     if( ! empty( $request_type ) && $request -> lang != 'en' ) {
                        $requestTypeName =  $request_type;
                        $tf = false;
                     } else {
                        $requestTypeName = ( ! empty( $this -> getNameRequestType( $datas[ 'request_type' ], 'en' ) ) ? $this -> getNameRequestType( $datas[ 'request_type' ], 'en' ) : $base_request_type );
                        $tf = true;
                     }  
                     if( $request -> lang == 'en' ) {
                        $tf = '';
                     }

                  array_push( $reasonsArray, [

                     'id' => $datas[ 'id' ],
   
                     'name' => $base_name,
   
                     'abbreviation' => ( $datas['abbreviation'] !== 'null' ? $datas['abbreviation'] : '' ),
   
                     'description' => $datas['description'],
   
                     'request_type' => $datas['request_type'],
   
                     'request_type_name' => $requestTypeName,
   
                     'brands' => $datas['brands'],
   
                     'org_id' => $datas['org_id'],
   
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'convertion_request_type' => $tf,
                     
                     // 'convertion_request_type' => false,

                     'language' => $language
   
                  ] );
               }
            }

        }

      return response()->json($reasonsArray);

   }

   public function store(Request $request)
   {
      
      try{
      
         $lang = $request->lang;
         
         $name = ucwords($request->name);

         $check = Reasons::all();
         
         $brand = $request -> brand;
      
         if( ! is_array( json_decode( $request -> brand ) ) && ! empty( $request -> brand ) ) {
   
            $brand = '['. $request -> brand .']';
   
         }

         foreach( $check as $reasons ) {
            $x0 = 'en';
            $x1 = 'it';
            $x2 = 'de';
            $x3 = 'ph-fil';
            $x4 = 'ph-bis';
            for( $x = 0; $x < 5; $x++ ) {
               $languange = $x+$x;
               
               $get_name = getTranslation($reasons->name, $languange);

               foreach( json_decode( $reasons -> brands ) as $c ) {
                     
                  foreach( json_decode( $brand ) as $br ) {

                     if( strtolower( $get_name ) == strtolower( $name ) && $reasons -> id != $request->id && $br == $c ){

                        return response()->json( [ 'duplicate' => true, 'name' => $name ] );
                        
                     }
                     
                  }

               }

            }
      
         }

         if (is_null($request->id)) {

            $reasons = new Reasons;

            $reasons->name = json_encode([
                                    $lang => $name,
                                 ]);

            $reasons->abbreviation = $request -> abbreviation;

            $reasons->description = $request -> description;

            $reasons->request_type = $request -> request_type;
            
            $reasons->brands = $brand;

            $reasons->org_id = $request -> org_id;

            $reasons->save();

            $reasons->name = getTranslation($reasons->name, $lang);

         } else {

            $reasons = Reasons::findOrFail($request->id);
            
            $reasonsVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $reasons -> name ) );

            $reasons->name = $reasonsVal;

            $reasons->abbreviation = $request -> abbreviation;

            $reasons->description = ( isset( $request -> description ) && ! empty( $request -> description ) ? $request -> description : null );

            $reasons->request_type = $request -> request_type;

            $reasons->brands = $request -> brand;

            $reasons->org_id = $request -> org_id;

            $reasons->update();
            
            $reasons->name = getTranslation($reasons->name, $lang);

         }

         return response()->json($reasons);

      } catch( \Exception $e ) {
      
         echo "<pre>";
      
         echo $e -> getMessage();
      
         echo "</pre>";
      
      }
   
   }

   public function destroy(Request $request)
   {
   
      Reasons::where('id',$request->id)->delete();

      return response()->json(true);
   
   }

   public function getName( Request $request ) {
      
      $reasons = Reasons::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $reasons->name) );
      
      return response() -> json($message);

  }

  public function linkBrand( Request $request ) 
  {
   
      try{
            
         $lang = $request->lang;

         $reasons = Reasons::findOrFail($request->id);

         $reasons -> brands = $request -> brand;
         
         $reasons -> update();
            
         $reasons -> name = getTranslation( $reasons -> name, $lang );

         return response()->json( $reasons );

      } catch( \Exception $e ) {
         
         echo "<pre>";
         
         echo $e -> getMessage();
         
         echo "</pre>";
      
      }

   }

   public function getNameReasons( $id, $lang ) {
        
      $reasons = Reasons::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $reasons -> name) );

      return $name;

  }

  public function getNameRequestType( $id, $lang ) 
  {
     $name = '';
      if( ! empty( $id ) ) {
         $request_type = RequestType::whereId( $id ) -> first();

         $name = ucwords( string_to_value( $lang, $request_type -> name) );
      }
      return $name;
      

   }
  
}
