<?php

namespace CRM\API\CalendarNotesType;

use App\Http\Controllers\Controller;
use App\Models\CalendarNotesType;
use App\Models\CalendarNotes;
use App\Models\CalendarNotesLocation;
use App\Models\RequestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CalendarNotesTypeController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;

      // $brand = $request -> brand;

      // $array_ids = [];

      // foreach( $calendar_notes_type as $get_calendar_notes_type ) {

      //    if( in_array( $request -> brand, json_decode( $get_calendar_notes_type -> brands ) ) == true ) {

      //       $array_ids[] = $get_calendar_notes_type -> id;

      //    }

      // }

      $calendarNotesArray = array();

      $keyword = $request -> search;

      $calendar_notes_type = CalendarNotesType::when( $keyword, function( $query ) use ( $keyword ) {

            if( ! empty( $keyword ) ) {

               
            $new_like_query = '(

               ( name LIKE "%' . ucwords( $keyword ) . '%" )

               OR

               ( color_hexcode LIKE "%' .ucwords( $keyword ) . '%" )

               )';

               return $query -> whereRaw( " ( " .$new_like_query . ")" );

            }

         } )

         -> get();

         if (!$request->sortbyLang || $request->sortbyLang === 'all') {
            $calendar_notes_type = $calendar_notes_type;
         } else {
            $lang = $request->sortbyLang;
            $calendar_notes_type = $calendar_notes_type
            ->filter(function ($term) {
               if (!$term->has_translation) return $term;
            })
            ->values();
         }

         $language = 'English';

         if( $lang == 'it') { $language = 'Italian'; } 
         if( $lang == 'de' ) { $language = 'German'; } 
         if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
         if( $lang == 'ph-bis' ) { $language = 'Visayan'; }

        foreach( $calendar_notes_type as $datas ) {
         
            $name = $this -> getNameCalendarNotesTypes( $datas[ 'id' ], $lang );

            $base_name = ( ! empty( $name ) ? $name : $this -> getNameCalendarNotesTypes( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

               $base_name_italy = $this -> getNameCalendarNotesTypes( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                  $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameCalendarNotesTypes( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;

                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameCalendarNotesTypes( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameCalendarNotesTypes( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }

            }
            
            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               array_push( $calendarNotesArray, [

                  'id' => $datas[ 'id' ],

                  'name' => $base_name,

                  'color_hexcode' => $datas['color_hexcode'],
                  
                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {
                  array_push( $calendarNotesArray, [

                     'id' => $datas[ 'id' ],
   
                     'name' => $base_name,
   
                     'color_hexcode' => $datas['color_hexcode'],
                     
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'language' => $language
   
                  ] );
               }
            }

        }

      return response()->json($calendarNotesArray);

   }

   public function store(Request $request)
   {
      try{

         $lang = $request->lang;
         
         $name = ucwords($request->name);

         $check = CalendarNotesType::all();

         foreach( $check as $calendar_notes_type ) {

            $x0 = 'en';
            $x1 = 'it';
            $x2 = 'de';
            $x3 = 'ph-fil';
            $x4 = 'ph-bis';
            for( $x = 0; $x < 5; $x++ ) {
               $languange = $x+$x;

               $get_name = getTranslation($calendar_notes_type -> name, $languange);

               if( strtolower( $get_name ) == strtolower( ucwords( $name ) ) && $calendar_notes_type -> id != $request->id ) {

                  return response()->json( [ 'duplicate' => true, 'name' => $name ] );

               }

            }

         }

         if (is_null($request->id)) {

            // $brand = $request -> brand;
      
            // if( ! is_array( json_decode( $request -> brand ) ) && ! empty( $request -> brand ) ) {
      
            //    $brand = '['. $request -> brand .']';
      
            // }

            $calendar_notes_type = new CalendarNotesType;

            $calendar_notes_type -> name = json_encode([
                                          $lang => $name,
                                       ]);

            $calendar_notes_type -> color_hexcode = $request -> color_hexcode;

            // $calendar_notes_type -> brands = $brand;

            $calendar_notes_type -> save();

            $calendar_notes_type -> name = $calendar_notes_type -> name;

         } else {

            $calendar_notes_type = CalendarNotesType::findOrFail($request->id);

            $calendar_notes_typeVal = string_add_json( $lang, ucwords( $request -> name ), string_remove( $lang, $calendar_notes_type -> name ) );

            $calendar_notes_type -> name = $calendar_notes_typeVal;

            $calendar_notes_type -> color_hexcode = $request -> color_hexcode;
            
            // $calendar_notes_type -> brands = $request -> brand;

            $calendar_notes_type->update();
            
            $calendar_notes_type->name = $calendar_notes_type -> name;

         }

         return response()->json($calendar_notes_type);

      } catch( \Exception $e ) {

         echo "<pre>";

         echo $e -> getMessage();

         echo "</pre>";

      }

   }

   public function destroy(Request $request)
   {

      CalendarNotesType::where('id',$request->id)->delete();
      
      return response()->json(true);

   }

   public function getName( Request $request ) {
      
      $calendar_notes_type = CalendarNotesType::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $calendar_notes_type->name) );
      
      return response() -> json($message);

  }

//   public function linkBrand( Request $request ) 
//   {

//       try{
         
//       $lang = $request->lang;

//       $calendar_notes_type = CalendarNotes::findOrFail($request->id);

//       $calendar_notes_type -> brands = $request -> brand;
      
//          $calendar_notes_type -> update();
            
//          $calendar_notes_type -> name = $calendar_notes_type -> color_name;

//          return response()->json( $calendar_notes_type );

//       } catch( \Exception $e ) {

//          echo "<pre>";
         
//          echo $e -> getMessage();
         
//          echo "</pre>";
      
//       }
   
//    }

   public function getNameCalendarNotesTypes( $id, $lang ) {
        
      $calendar_notes_type = CalendarNotesType::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $calendar_notes_type -> name) );

      return $name;

  }

}
