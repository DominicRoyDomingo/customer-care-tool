<?php

namespace CRM\API\ColorCoding;

use App\Http\Controllers\Controller;
use App\Models\ColorCoding;
use App\Models\Brand;
use App\Models\RequestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ColorCodingController extends Controller
{

   public function index(Request $request)
   {

      $lang = $request->lang;

      $keyword = $request -> search;

      $color_coding_connection = ColorCoding::select( [ 'id', 'brands' ] ) -> where( 'org_id', '=', $request -> org_id ) 

      -> when( $keyword, function( $query ) use ( $keyword ) {

         if( ! empty( $keyword ) ) {

            $new_like_query = '(

               ( color_name LIKE "%' . ucwords( $keyword ) . '%" )

               OR

               ( hexcode LIKE "%' .ucwords( $keyword ) . '%" )

               OR 

               ( description LIKE "%' .ucwords( $keyword ) . '%" )

               OR 

               ( slot_limit LIKE "%' .ucwords( $keyword ) . '%" )

               )';

               return $query -> whereRaw( " ( " .$new_like_query . ")" );

            }

         } )

         -> get();

         if (!$request->sortbyLang || $request->sortbyLang === 'all') {
            $color_coding_connection = $color_coding_connection;
         } else {
            $lang = $request->sortbyLang;
            $color_coding_connection = $color_coding_connection
            ->filter(function ($term) {
               if (!$term->has_translation) return $term;
            })
            ->values();
         }

         $array_ids = [];

         foreach( $color_coding_connection as $get_brands ) {

            if( in_array( $request -> brand, json_decode( $get_brands -> brands ) ) == true ) {

               $array_ids[] = $get_brands -> id;

            }

         }
      
         $color_coding = ColorCoding::whereIn( 'id', $array_ids ) -> get()->map(function ($org) use($lang){

            $org['brand'] = Brand::whereId( $org['brands'] ) -> select( [ 'name' ] ) -> first();

            return $org;
      
         });

         $ColorCodingArray = array();

         $language = 'English';

         if( $lang == 'it') { $language = 'Italian'; } 
         if( $lang == 'de' ) { $language = 'German'; } 
         if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
         if( $lang == 'ph-bis' ) { $language = 'Visayan'; }

         foreach( $color_coding as $datas ) {
         
            $name = $this -> getNameColorCoding( $datas[ 'id' ], $lang );

            $base_name = ( ! empty( $name ) ? $name : $this -> getNameColorCoding( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

               $base_name_italy = $this -> getNameColorCoding( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                  $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameColorCoding( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameColorCoding( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameColorCoding( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }

            }
            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {

               array_push( $ColorCodingArray, [

                  'id' => $datas[ 'id' ],

                  'color_name' => $base_name,

                  'description' => $datas['description'],
                  
                  'brand' => $datas['brand'],
                  
                  'brands' => $datas['brands'],
                  
                  'created_by_superadmin' => $datas['created_by_superadmin'],
                  
                  'hexcode' => $datas['hexcode'],
                  
                  'org_id' => $datas['org_id'],
                  
                  'slot_limit' => $datas['slot_limit'],
                  
                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {
                  array_push( $ColorCodingArray, [

                     'id' => $datas[ 'id' ],
   
                     'color_name' => $base_name,
   
                     'description' => $datas['description'],
                     
                     'brand' => $datas['brand'],
                     
                     'brands' => $datas['brands'],
                     
                     'created_by_superadmin' => $datas['created_by_superadmin'],
                     
                     'hexcode' => $datas['hexcode'],
                     
                     'org_id' => $datas['org_id'],
                     
                     'slot_limit' => $datas['slot_limit'],
                     
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'language' => $language
   
                  ] );
               }
            }

        }

      return response()->json($ColorCodingArray);

   }

   public function store(Request $request)
   {
 
      try{
 
         $lang = $request->lang;
         
 
         $name = ucwords($request->color_name);
         
         $brand = $request -> brand;
 
         if( ! is_array( json_decode( $request -> brand ) ) && ! empty( $request -> brand ) ) {

            $brand = '['. $request -> brand .']';

         }

         $check = ColorCoding::all();

         foreach( $check as $color_coding ) {
            $x0 = 'en';
            $x1 = 'it';
            $x2 = 'de';
            $x3 = 'ph-fil';
            $x4 = 'ph-bis';
            for( $x = 0; $x < 5; $x++ ) {
               $languange = $x+$x;

               $get_name = getTranslation($color_coding -> color_name, $languange);
               
               foreach( json_decode( $color_coding -> brands ) as $c ) {
                     
                  foreach( json_decode( $brand ) as $br ) {

                     if( strtolower( $get_name ) == strtolower( $name ) && $color_coding -> id != $request->id && $br == $c ){

                        return response()->json( [ 'duplicate' => true, 'name' => $name ] );

                     }
                     
                  }

               }
            }

            if( $request -> slot_limit == 'Not Available' ) {
               $check_color_coding = ColorCoding::where( 'id', '!=', $request -> id ) -> where( 'slot_limit', '=', 'Not Available' ) -> get();
               if( $check_color_coding -> count() > 0 ){
                  foreach( $check_color_coding as $check_brand ){
                     foreach( json_decode( $check_brand -> brands ) as $c ) {
                        foreach( json_decode( $brand ) as $br ) {
                           if( $br == $c ){
                              return response() -> json( [ 'not_available' => true, 'name' => $name ] );
                           }  
                        }
                     }
                  }
               }
            }
         }

         if (is_null($request->id)) {

            $color_coding = new ColorCoding;

            $color_coding -> color_name = json_encode([
                                          $lang => $request -> color_name,
                                       ]);

            $color_coding -> hexcode = $request -> hexcode;

            $color_coding -> description = $request -> description;

            $color_coding -> slot_limit = $request -> slot_limit;
            
            $color_coding -> brands = $brand;

            $color_coding -> org_id = $request -> org_id;

            $color_coding -> save();

            $color_coding -> name = $color_coding -> color_name;

         } else {

            $color_coding = ColorCoding::findOrFail($request->id);

            $color_codingVal = string_add_json( $lang, ucwords( $request -> color_name ), string_remove( $lang, $color_coding -> color_name ) );

            $color_coding -> color_name = $color_codingVal;

            $color_coding -> hexcode = $request -> hexcode;

            $color_coding -> description = ( isset( $request -> description ) && ! empty( $request -> description ) ? $request -> description : null );

            $color_coding -> slot_limit = $request -> slot_limit;
            
            $color_coding -> brands = $request -> brand;

            $color_coding -> org_id = $request -> org_id;

            $color_coding->update();
            
            $color_coding->name = $color_coding -> color_name;

         }

         return response()->json($color_coding);

      } catch( \Exception $e ) {
 
         echo "<pre>";
 
         echo $e -> getMessage();
 
         echo "</pre>";
 
      }
 
   }

   public function destroy(Request $request)
   {
 
      ColorCoding::where('id',$request->id)->delete();

      return response()->json(true);
 
   }

   public function getName( Request $request ) {
      
      $color_coding = ColorCoding::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $color_coding->color_name) );
      
      return response() -> json($message);

  }

  public function linkBrand( Request $request ) {
 
   try{
         
         $lang = $request->lang;

         $color_coding = ColorCoding::findOrFail($request->id);
         
         
         if( $color_coding -> slot_limit == 'Not Available' ) {
            $test = [];
            $color_coding_connection = ColorCoding::select( [ 'id', 'brands', 'slot_limit' ] ) -> where('slot_limit', 'Not Available') -> get();
            foreach( $color_coding_connection as $get_brands ) {
               foreach(  json_decode( $get_brands -> brands ) as $brands ) {
                  if( $brands != $request -> session_brand ) {
                     foreach( json_decode( $request -> brand ) as $get_brand ){
                        if( $get_brand != $request -> session_brand ) {
                           if( $brands == $get_brand ) {
                              $color_coding -> name = ucwords( string_to_value( $lang , $color_coding -> color_name ) );
                              return response()->json( [ 'success' => false, 'name' => $color_coding ] );
                           }
                        }
                     }
                  }
               }
            }
         }
         $color_coding -> brands = $request -> brand;
         
         $color_coding -> update();
            
         $color_coding -> name = ucwords( string_to_value( $lang , $color_coding -> color_name ) );

         return response()->json( $color_coding );
 
      } catch( \Exception $e ) {
 
         echo "<pre>";
 
         echo $e -> getMessage();
 
         echo "</pre>";
 
      }
 
   }

   public function checkSlotItem( Request $request ) {
      $brand = $request -> brand;
      $id = $request -> id;
      $slot = $request -> slot;
      $color_coding = ColorCoding::all();
      $success = 'false';
      foreach( $color_coding as $key => $color ) {
         if( $color -> slot_limit == 'Not Available' && in_array( $brand, json_decode( $color -> brands ) ) && $color -> id !== $id ) {
            $success = 'true';
         }
      }
      return response() -> json( $success );
   }

   public function getNameColorCoding( $id, $lang ) {
        
      $color_coding_name = ColorCoding::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $color_coding_name -> color_name ) );

      return $name;

  }
}
