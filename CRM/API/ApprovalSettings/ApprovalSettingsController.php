<?php

namespace CRM\API\ApprovalSettings;

use App\Http\Controllers\Controller;
use App\Models\ApprovalSettings;
use App\Models\ApprovalRequestType;
use App\Models\Brand;
use App\Models\RequestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\ColorCoding;

class ApprovalSettingsController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;

      $brand = $request -> brand;

      $language = 'English';

      $keyword = $request -> search;

      $color_coding = ApprovalSettings::where( 'brands', '=', $brand ) 

      -> when( $keyword, function( $query ) use ( $keyword ) {

         if( ! empty( $keyword ) ) {

            $new_like_query = ' name LIKE "%' .  ucwords( $keyword ) . '%" ';

            return $query -> whereRaw( " ( " .$new_like_query . ")" );

         }

      } )

      -> get();

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $color_coding = $color_coding;
      } else {
         $lang = $request->sortbyLang;
         $color_coding = $color_coding
         ->filter(function ($term) {
            if (!$term->has_translation) return $term;
         })
         ->values();
      }
      
      if( $lang == 'it') { $language = 'Italian'; } 
      if( $lang == 'de' ) { $language = 'German'; } 
      if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
      if( $lang == 'ph-bis' ) { $language = 'Visayan'; }

      $approvalSettings = array();

        foreach( $color_coding as $datas ) {
         
            $name = $this -> getNameApprovalSettings( $datas[ 'id' ], $lang );

            $base_name = ( ! empty( $name ) ? $name : $this -> getNameApprovalSettings( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

               $base_name_italy = $this -> getNameApprovalSettings( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                  $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameApprovalSettings( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameApprovalSettings( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameApprovalSettings( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }

            }
            
            $approval_type_list = array();

            $request_type_list = array();

            $approval_type = ApprovalRequestType::where( 'approval_setting', '=', $datas[ 'id' ] ) 

            -> join( 'request_type as rt', 'rt.id', 'approval_request_type.request_type' )

            -> select( [ 'approval_request_type.request_type', 'approval_request_type.approval_type', 'rt.name', 'rt.id'  ] ) 

            -> get();

            foreach( $approval_type as $type ) {

               $space = ' ';

               $name_request_type = $this -> getNameRequestType( $type -> id, $lang );

               $name_request_type_base_name = ( ! empty( $name_request_type ) ? $name_request_type : $this -> getNameRequestType( $type -> id, 'en' ) );

               if( $name_request_type == '' && $name_request_type_base_name == '' ) {

                  $base_name_italy_request_type = $this -> getNameRequestType( $type -> id, 'it' );

                  if( $base_name_italy_request_type !== '' ) {

                     $name_request_type_base_name = $base_name_italy_request_type;

                  } else {
      
                     $base_name_germany_request_type = $this -> getNameRequestType( $type -> id, 'de' );
   
                     if( $base_name_germany_request_type !== '' ) {
   
                        $name_request_type_base_name = $base_name_germany_request_type;
      
                     }
   
                     else 
   
                     {
   
                        $base_name_filipino_request_type = $this -> getNameRequestType( $type -> id, 'ph-fil' );
   
                        if( $base_name_filipino_request_type !== '' ) {
   
                           $name_request_type_base_name = $base_name_filipino_request_type;
      
                        }
   
                        else{
   
                           $name_request_type_base_name = $this -> getNameRequestType( $type -> id, 'ph-bis' );
                           
                        }
   
                     }

                  }

               }

               $request_type_name = $name_request_type_base_name;
               
               if( $type -> approval_type == 'If Overbook Only' ) {

                  $request_type_name = $name_request_type_base_name. ' [' . $type -> approval_type . '] ';

               }

               

               array_push( $approval_type_list, $request_type_name . ( ! empty( $name_request_type ) ? '' : '(No '. $language .' translation yet)' )  );

               if( ! empty( $request_type_name ) && $request -> lang != 'en' ) {
                  $requestTypeName =  $request_type_name;
               } else {
                  $requestTypeName =  ( ! empty( $this -> getNameRequestType( $type -> id, 'en' ) ) ? $this -> getNameRequestType( $type -> id, 'en' ) : $name_request_type_base_name );
               }  

               $request_type_list[] = [

                  'id' => $type -> request_type,

                  'value' => $type -> approval_type,

                  'request_type' => $requestTypeName,

                  'convertion' => ( ! empty( $name_request_type ) ? false : true )

               ];

            }

            $approval_type_list_sb = substr(str_replace('"', '', json_encode( $approval_type_list ) ), 1);

            $approval_type_list_final = substr($approval_type_list_sb, 0, -1);

            $request_type_array = array();

            $request_type_connection = RequestType::select( [ 'id', 'brands' ] ) -> get();

            $array_ids = [];

            foreach( $request_type_connection as $get_brands ) {

               if( in_array( $request -> brand, json_decode( $get_brands -> brands ) ) == true ) {

                  $array_ids[] = $get_brands -> id;

               }

            }

            $requestType = RequestType::whereIn( 'id', $array_ids ) -> get();

            foreach( $requestType as $request_ ) {

               $name_request_type = $this -> getNameRequestType( $request_ -> id, $lang );

               $name_request_type_base_name = ( ! empty( $name_request_type ) ? $name_request_type : $this -> getNameRequestType( $request_ -> id, 'en' ) );

               if( $name_request_type == '' && $name_request_type_base_name == '' ) {

                  $base_name_italy_request_type = $this -> getNameRequestType( $request_ -> id, 'it' );

                  if( $base_name_italy_request_type !== '' ) {

                     $name_request_type_base_name = $base_name_italy_request_type;

                  } else {

                     $base_name_germany_request_type = $this -> getNameRequestType( $request_ -> id, 'de' );
   
                     if( $base_name_germany_request_type !== '' ) {
   
                        $name_request_type_base_name = $base_name_germany_request_type;
      
                     }
   
                     else 
   
                     {
   
                        $base_name_filipino_request_type = $this -> getNameRequestType( $datas[ 'id' ], 'ph-fil' );
   
                        if( $base_name_filipino_request_type !== '' ) {
   
                           $name_request_type_base_name = $base_name_filipino_request_type;
      
                        }
   
                        else{
   
                           $name_request_type_base_name = $this -> getNameRequestType( $datas[ 'id' ], 'ph-bis' );
                           
                        }
   
                     }

                  }

               }

               $approval_type = "";

               $approval = ApprovalRequestType::where('request_type','=', $request_ -> id ) -> where( 'approval_setting', '=', $datas[ 'id' ]) -> select( [ 'approval_type' ] );

               if( $approval -> count()  > 0 ) {

                  $approval_type = $approval -> first() -> approval_type;

               }

               $request_type_array[] = [

                  'id' => $request_ -> id,

                  'settings_id' => $datas[ 'id' ],

                  'name' => $name_request_type_base_name,

                  'convertion' => ( ! empty( $name_request_type ) ? false : true ),

                  'approval' => $approval_type

               ];

            }
            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               array_push( $approvalSettings, [

                  'id' => $datas[ 'id' ],

                  'name' => $base_name,

                  'admin_approval' => $datas['admin_approval'],

                  'overbooking' => $datas['overbooking'],

                  'approval_type' => $approval_type_list_final,

                  'request_type_approval' => $request_type_list,

                  'request_type' => $request_type_array,

                  'brands' => $datas['brands'],

                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {
                  
                  array_push( $approvalSettings, [

                     'id' => $datas[ 'id' ],
   
                     'name' => $base_name,
   
                     'admin_approval' => $datas['admin_approval'],
   
                     'overbooking' => $datas['overbooking'],
   
                     'approval_type' => $approval_type_list_final,
   
                     'request_type_approval' => $request_type_list,
   
                     'request_type' => $request_type_array,
   
                     'brands' => $datas['brands'],
   
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'language' => $language
   
                  ] );
               }
            }

        }

      return response()->json($approvalSettings);

   }

   public function store(Request $request)
   {

      try{

         $lang = $request->lang;
         
         $name = ucwords($request->name);

         $check = ApprovalSettings::all();

         foreach( $check as $approval_settings ) {
            $x0 = 'en';
            $x1 = 'it';
            $x2 = 'de';
            $x3 = 'ph-fil';
            $x4 = 'ph-bis';
            for( $x = 0; $x < 5; $x++ ) {
               $languange = $x+$x;

               $get_name = getTranslation($approval_settings -> name, $languange);

               if( strtolower( $get_name ) == strtolower( $name ) && $approval_settings -> id != $request->id && $request -> brands == $approval_settings -> brands ) {

                  return response()->json( [ 'duplicate' => true, 'name' => $name ] );

               }
            }

         }

         if (is_null($request->id)) {

            $brand = $request -> brands;

            $approval_settings = new ApprovalSettings;

            $approval_settings -> name = json_encode([
                                          $lang => $name,
                                       ]);

            $approval_settings -> admin_approval = $request -> admin_approval;

            $approval_settings -> overbooking = $request -> overbooking;
            
            $approval_settings -> brands = $brand;

            $approval_settings -> save();

            $approval_settings -> name = $approval_settings -> name;

         } else {

            $approval_settings = ApprovalSettings::findOrFail($request->id);

            $approval_settingsVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $approval_settings -> name ) );

            $approval_settings -> name = $approval_settingsVal;

            $approval_settings -> admin_approval = $request -> admin_approval;

            $approval_settings -> overbooking = $request -> overbooking;
            
            $approval_settings -> brands = $request -> brands;

            $approval_settings->update();
            
            $approval_settings->name = $approval_settings -> name;

            ApprovalRequestType::where('approval_setting', '=', $request->id ) -> delete();

         }

         $approval_settings_id = $approval_settings -> id;

         if( $request -> admin_approval == 1 ) {

            foreach( json_decode( $request -> approval_setting ) as $approval ) {

               if( $approval -> value !== 'Off' ) {

                  $approve_type = new ApprovalRequestType;

                  $approve_type -> approval_setting = $approval_settings_id;

                  $approve_type -> request_type = $approval -> id;

                  $approve_type -> approval_type = $approval -> value;

                  $approve_type -> save();

               }

            }

         }

         return response()->json($approval_settings);

      } catch( \Exception $e ) {

         echo "<pre>";

         echo $e -> getMessage();

         echo "</pre>";

      }

   }

   public function destroy(Request $request)
   {

      ApprovalSettings::where('id',$request->id)->delete();

      ApprovalRequestType::where('approval_setting',$request->id)->delete();

      return response()->json(true);

   }

   public function getName( Request $request ) {
      
      $approval_settings = ApprovalSettings::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $approval_settings->name) );
      
      return response() -> json($message);

  }

  public function linkBrand( Request $request ) 
  {

      try{
         
      $lang = $request->lang;

      $color_coding = ColorCoding::findOrFail($request->id);

      $color_coding -> brands = $request -> brand;
      
         $color_coding -> update();
            
         $color_coding -> name = $color_coding -> color_name;

         return response()->json( $color_coding );

      } catch( \Exception $e ) {

         echo "<pre>";
         
         echo $e -> getMessage();
         
         echo "</pre>";
      
      }
   
   }

   public function getNameApprovalSettings( $id, $lang ) {
        
      $reasons = ApprovalSettings::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $reasons -> name) );

      return $name;

  }

  public function getNameRequestType( $id, $lang ) {
      
      // if( ! empty ( $id ) ) {

         $request_type = RequestType::whereId( $id ) -> first();

         $name = ucwords( string_to_value( $lang, $request_type['name'] ) );

         return $name;
      // }

   }

}
