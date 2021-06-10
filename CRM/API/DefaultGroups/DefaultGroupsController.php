<?php

namespace CRM\API\DefaultGroups;

use App\Http\Controllers\Controller;
use App\Models\DefaultGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DefaultGroupsController extends Controller
{

   public function index(Request $request)
   {

      $lang = $request->lang;
 
      $keyword = $request -> search;
 
      $default_groups_connection = DefaultGroups::select( [ 'id', 'brands' ] ) 
 
      -> when( $keyword, function( $query ) use ( $keyword ) {

         if( ! empty( $keyword ) ) {

            $new_like_query = ' name LIKE "%' .  ucwords( $keyword ) . '%" ';

            return $query -> whereRaw( " ( " .$new_like_query . ")" );

         }

      } )

      -> where( 'org_id', '=', $request -> org_id ) 

      -> get();
      
      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $default_groups_connection = $default_groups_connection;
      } else {
         $lang = $request->sortbyLang;
         $default_groups_connection = $default_groups_connection
         ->filter(function ($term) {
            if (!$term->has_translation) return $term;
         })
         ->values();
      }

      $array_ids = [];

      foreach( $default_groups_connection as $get_brands ) {

         if( in_array( $request -> brand, json_decode( $get_brands -> brands ) ) == true ) {

            $array_ids[] = $get_brands -> id;

         }

      }
      
      $language = 'English';

      if( $lang == 'it') { $language = 'Italian'; } 
      if( $lang == 'de' ) { $language = 'German'; } 
      if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
      if( $lang == 'ph-bis' ) { $language = 'Visayan'; }

      $requestType = DefaultGroups::whereIn( 'id', $array_ids ) -> get();

      $requestTypeArray = array();

        foreach( $requestType as $datas ) {
         
            $name = $this -> getNameDefaultGroups( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getNameDefaultGroups( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getNameDefaultGroups( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

               }  else {

                  $base_name_germany = $this -> getNameDefaultGroups( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameDefaultGroups( $datas[ 'id' ], 'ph-fil' );
   
                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameDefaultGroups( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }
                
            }
            
            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               array_push( $requestTypeArray, [

                  'id' => $datas[ 'id' ],

                  'name' => $base_name,

                  'brands' => $datas['brands'],

                  'org_id' => $datas['org_id'],

                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {
                  array_push( $requestTypeArray, [

                     'id' => $datas[ 'id' ],
   
                     'name' => $base_name,
   
                     'brands' => $datas['brands'],
   
                     'org_id' => $datas['org_id'],
   
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'language' => $language
   
                  ] );
               }
            }

        }

      return response()->json($requestTypeArray);

   }

   public function store(Request $request)
   {

      $lang = $request->lang;
      
      $default_groups = ucwords($request->name);

      $check = DefaultGroups::all();


      $brand = $request -> brand;

      if( ! is_array( json_decode( $request -> brand ) ) && ! empty( $request -> brand ) ) {

         $brand = '['. $request -> brand .']';

      }


      foreach( $check as $req_type ) {
         $x0 = 'en';
         $x1 = 'it';
         $x2 = 'de';
         $x3 = 'ph-fil';
         $x4 = 'ph-bis';
         for( $x = 0; $x < 5; $x++ ) {
            $languange = $x+$x;

            $get_name = getTranslation($req_type->name, $languange);

            foreach( json_decode( $req_type -> brands ) as $c ) {
                     
               foreach( json_decode( $brand ) as $br ) {
                  
                  if( strtolower( $get_name ) == strtolower( $default_groups ) && $req_type -> id != $request->id && $br == $c ){
                     
                     return response()->json( [ 'duplicate' => true, 'name' => $default_groups ] );

                  }
                  
               }

            }

         }

      }

      if (is_null($request->id)) {

         $requestType = new DefaultGroups;

         $requestType->name = json_encode([
                                 $lang => $default_groups,
                              ]);

         $requestType->brands = $brand;

         $requestType->org_id = $request -> org_id;

         $requestType->save();

         $requestType->default_groups = getTranslation($requestType->name, $lang);

      } else {

         $requestType = DefaultGroups::findOrFail($request->id);

         $requestTypeVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $requestType -> name ) );

         $requestType->name = $requestTypeVal;

         $requestType->brands = $request -> brand;

         $requestType->org_id = $request -> org_id;

         $requestType->update();
         
         $requestType->name = getTranslation($requestType->name, $lang);

      }

      return response()->json($requestType);

   }

   public function destroy(Request $request){

      DefaultGroups::where('id',$request->id)->delete();

      return response()->json(true);
      
   }

   public function getName( Request $request ) {
      
      $requestType = DefaultGroups::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $requestType->name) );
      
      return response() -> json($message);

  }

  public function linkBrand( Request $request ) 
  {
      try{
         
         $lang = $request->lang;

         $requestType = DefaultGroups::findOrFail($request->id);

         $requestType -> brands = $request -> brand;
         
         $requestType -> update();
            
         $requestType -> name = getTranslation( $requestType -> name, $lang );

         return response()->json( $requestType );

      } catch( \Exception $e ) {
 
         echo "<pre>";
 
         echo $e -> getMessage();
 
         echo "</pre>";
 
      }
 
   }

   public function getNameDefaultGroups( $id, $lang ) {
        
      $default_groups = DefaultGroups::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $default_groups -> name) );

      return $name;

  }

}
