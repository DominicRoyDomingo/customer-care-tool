<?php

namespace CRM\API\RequestType;

use App\Http\Controllers\Controller;
use App\Models\RequestType;
use App\Models\Reasons;
use App\Models\ApprovalRequestType;
use App\Models\DefaultGroups;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RequestTypeController extends Controller
{

   public function index(Request $request)
   {
 
      $lang = $request->lang;

      $keyword = $request -> search;

      $request_type_connection = RequestType::select( [ 'id', 'brands' ] ) 

      -> when( $keyword, function( $query ) use ( $keyword ) {

         if( ! empty( $keyword ) ) {

            $new_like_query = '

            (

               ( name LIKE "%' . ucwords( $keyword ) . '%" )

               )

               ';
            return $query -> whereRaw( " ( " .$new_like_query . ")" );

         }

      } )

      -> where( 'org_id', '=', $request -> org_id ) 

      -> get();

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $request_type_connection = $request_type_connection;
      } else {
         $lang = $request->sortbyLang;
         $request_type_connection = $request_type_connection
         ->filter(function ($term) {
            if (!$term->has_translation) return $term;
         })
         ->values();
      }
      
      $array_ids = [];

      foreach( $request_type_connection as $get_brands ) {

         if( in_array( $request -> brand, json_decode( $get_brands -> brands ) ) == true ) {

            $array_ids[] = $get_brands -> id;

         }

      }
      
      $language = 'English';

      if( $lang == 'it') { $language = 'Italian'; } 
      if( $lang == 'de' ) { $language = 'German'; } 
      if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
      if( $lang == 'ph-bis' ) { $language = 'Visayan'; }
      
      $requestType = RequestType::whereIn( 'id', $array_ids ) -> get();

      $requestTypeArray = array();

        foreach( $requestType as $datas ) {
         
            $name = $this -> getNameRequestType( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getNameRequestType( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getNameRequestType( $datas[ 'id' ], 'it' );

                if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

                } else {

                  $base_name_germany = $this -> getNameRequestType( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameRequestType( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameRequestType( $datas[ 'id' ], 'ph-bis' );
                        
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
      
      $request_type = ucwords($request->request_type);

      $check = RequestType::all();

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
                     
                     if( strtolower( $get_name ) == strtolower( $request_type ) && $req_type -> id != $request->id && $br == $c ){
                        
                        return response()->json( [ 'duplicate' => true, 'name' => $request_type ] );

                     }
                     
                  }

               }

            }

         }

      if (is_null($request->id)) {

         $requestType = new RequestType;

         $requestType->name = json_encode([
                                 $lang => $request_type,
                              ]);

         $requestType->affect_limit = ( $request -> is_affect_limit == true ? 1 : 0 );

         $requestType->brands = $brand;

         $requestType->org_id = $request -> org_id;

         $requestType->save();

         $requestType->request_type = getTranslation($requestType->name, $lang);

      } else {

         $requestType = RequestType::findOrFail($request->id);

         $requestTypeVal = string_add_json( $lang, ucwords( $request -> request_type ), string_remove( $lang, $requestType -> name ) );

         $requestType->name = $requestTypeVal;

         $requestType->affect_limit = ( $request -> is_affect_limit == true ? 1 : 0 );

         $requestType->brands = $request -> brand;

         $requestType->org_id = $request -> org_id;

         $requestType->update();
         
         $requestType->name = getTranslation($requestType->name, $lang);

      }

      return response()->json($requestType);
   }

   public function destroy(Request $request)
   {

      $get_reason = Reasons::all();

      foreach( $get_reason as $reason ) {

         if( $request->id == $reason -> request_type ) {

            $request_name = RequestType::where('id',$request->id)->select( [ 'name' ] ) -> first() -> name;

            return response()->json( [ 'inUse' => true, 'name' => ucwords( string_to_value( $request->lang , $request_name) ) ] );

         }

      }

      $approval_type = ApprovalRequestType::all();

      foreach( $approval_type as $approval_types ) {

         if( $request->id == $approval_types -> request_type ) {

            $request_name = RequestType::where('id',$request->id)->select( [ 'name' ] ) -> first() -> name;

            return response()->json( [ 'inUse' => true, 'name' => ucwords( string_to_value( $request->lang , $request_name) ) ] );

         }

      }

      RequestType::where('id',$request->id)->delete();

      return response()->json(true);

   }

   public function getName( Request $request ) {
      
      $requestType = RequestType::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $requestType->name) );
      
      return response() -> json($message);

  }

  public function linkBrand( Request $request ) 
  {

      try{
         
         $lang = $request->lang;

         $requestType = RequestType::findOrFail($request->id);

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

   public function getNameRequestType( $id, $lang ) 
   {
        
      $request_type = RequestType::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $request_type -> name) );

      return $name;

  }

}
