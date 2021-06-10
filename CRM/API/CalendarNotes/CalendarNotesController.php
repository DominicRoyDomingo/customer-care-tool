<?php

namespace CRM\API\CalendarNotes;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequestType;
use App\Models\Brand;

use App\Models\CalendarNotes;
use App\Models\CalendarNotesLocation;
use App\Models\CalendarNotesType;
use App\Models\RequestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CalendarNotesController extends Controller
{

   public function index(Request $request)
   {
      $lang = $request->lang;

      $brand = $request -> brand;

      $keyword = $request -> search;

      $calendar_notes = CalendarNotes::select( [ 'id', 'brands' ] ) 

      -> when( $keyword, function( $query ) use ( $keyword ) {

         if( ! empty( $keyword ) ) {

            $new_like_query = ' title LIKE "%' .  ucwords( $keyword ) . '%" ';

            return $query -> whereRaw( " ( " .$new_like_query . ")" );

         }

      } )

      -> get();

      if (!$request->sortbyLang || $request->sortbyLang === 'all') {
         $calendar_notes = $calendar_notes;
      } else {
         $lang = $request->sortbyLang;
         $calendar_notes = $calendar_notes
         ->filter(function ($term) {
            if (!$term->has_translation) return $term;
         })
         ->values();
      }

      $array_ids = [];

      foreach( $calendar_notes as $get_calendar_notes ) {

         if( in_array( $request -> brand, json_decode( $get_calendar_notes -> brands ) ) == true ) {

            $array_ids[] = $get_calendar_notes -> id;

         }

      }

      $language = 'English';

      if( $lang == 'it') { $language = 'Italian'; } 
      if( $lang == 'de' ) { $language = 'German'; } 
      if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
      if( $lang == 'ph-bis' ) { $language = 'Visayan'; }

      $calendarNotesArray = array();

      $getCalendarNotes = CalendarNotes::whereIn( 'id', $array_ids ) -> get();

        foreach( $getCalendarNotes as $datas ) {
         
            $name = $this -> getNameCalendarNotes( $datas[ 'id' ], $lang );

            $base_name = ( ! empty( $name ) ? $name : $this -> getNameCalendarNotes( $datas[ 'id' ], 'en' ) );

            
            if( $name == '' && $base_name == '' ) {
               
               $base_name_italy = $this -> getNameCalendarNotes( $datas[ 'id' ], 'it' );

               if( $base_name_italy !== '' ) {

                  $base_name = $base_name_italy;

               } else {

                  $base_name_germany = $this -> getNameCalendarNotes( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name = $base_name_germany;

                  }

                  else 

                  {

                     $base_name_filipino = $this -> getNameCalendarNotes( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getNameCalendarNotes( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

               }

            }


            // $type = CalendarNotesType::where( 'id', '=', $datas['type'] ) -> select( 'name' ) -> first();
            
            $name_type = $this -> getNameCalendarNotesType( $datas[ 'type' ], $lang );

            $base_name_type = ( ! empty( $name_type ) ? $name_type : $this -> getNameCalendarNotesType( $datas[ 'type' ], 'en' ) );
           
            if( $name_type == '' && $base_name_type == '' ) {
               
               $base_name_type_italy = $this -> getNameCalendarNotesType( $datas[ 'type' ], 'it' );
               
               if( $base_name_type_italy !== '' ) {

                  $base_name_type = $base_name_type_italy;

               } else {

                  $base_name_germany = $this -> getNameCalendarNotesType( $datas[ 'type' ], 'de' );

                  if( $base_name_germany !== '' ) {

                     $base_name_type = $base_name_germany;

                  }

                  else 

                  {

                     $base_type_name_filipino = $this -> getNameCalendarNotesType( $datas[ 'type' ], 'ph-fil' );

                     if( $base_type_name_filipino !== '' ) {

                        $base_name_type = $base_type_name_filipino;
   
                     }

                     else{

                        $base_name_type = $this -> getNameCalendarNotesType( $datas[ 'type' ], 'ph-bis' );
                        
                     }

                  }

               }

            }

            if (!$request->sortbyLang || $request->sortbyLang === 'all' ) {
               array_push( $calendarNotesArray, [

                  'id' => $datas[ 'id' ],

                  'title' => $base_name,

                  'content' => $datas['content'],

                  'type_name' => $base_name_type,
                  
                  'type' => $datas['type'],

                  'target_date_from_to' => ( $datas['target_date_from'] == $datas['target_date_to'] ? date('j F Y', strtotime( $datas['target_date_from'] ) ): date('j F Y', strtotime( $datas['target_date_from'] ) ) . ' - ' . date('j F Y', strtotime( $datas['target_date_to'] ) ) ),

                  'target_date_from' => $datas['target_date_from'],

                  'target_date_to' => $datas['target_date_to'],

                  'target_location' => $datas['target_location'],

                  'country' => CalendarNotesLocation::join('world_countries as wc','wc.id', 'calendar_notes_location.country') -> select( [ 'wc.id', 'wc.name' ] ) -> where('calendar_notes_location.calendar_notes_id', '=', $datas[ 'id' ] ) -> get(),

                  'brands' => $datas['brands'],

                  'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                  'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                  'convertion' => ( ! empty( $name ) ? false : true ),

                  'convertion_type' => ( ! empty( $name_type ) ? false : true ),

                  'language' => $language

               ] );
            } else {
               if( empty( $name ) ) {

                  if( ! empty( $name_type ) && $request -> lang != 'en' ) {
                     $requestTypeName =  $name_type;
                     $tf = false;
                  } else {
                     $requestTypeName = $this -> getNameCalendarNotesType( $datas[ 'type' ], 'en' );
                     $tf = true;
                  } 

                  array_push( $calendarNotesArray, [

                     'id' => $datas[ 'id' ],
   
                     'title' => $base_name,
   
                     'content' => $datas['content'],
   
                     'type_name' => $requestTypeName,
                     
                     'type' => $datas['type'],
   
                     'target_date_from_to' => ( $datas['target_date_from'] == $datas['target_date_to'] ? date('j F Y', strtotime( $datas['target_date_from'] ) ): date('j F Y', strtotime( $datas['target_date_from'] ) ) . ' - ' . date('j F Y', strtotime( $datas['target_date_to'] ) ) ),
   
                     'target_date_from' => $datas['target_date_from'],
   
                     'target_date_to' => $datas['target_date_to'],
   
                     'target_location' => $datas['target_location'],
   
                     'country' => CalendarNotesLocation::join('world_countries as wc','wc.id', 'calendar_notes_location.country') -> select( [ 'wc.id', 'wc.name' ] ) -> where('calendar_notes_location.calendar_notes_id', '=', $datas[ 'id' ] ) -> get(),
   
                     'brands' => $datas['brands'],
   
                     'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),
   
                     'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),
   
                     'convertion' => ( ! empty( $name ) ? false : true ),
   
                     'convertion_type' => ( ! empty( $tf ) ? false : true ),
   
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
         
         $title = ucwords($request->title);

         $check = CalendarNotes::all();

         $brand = $request -> brand;
      
         if( ! is_array( json_decode( $request -> brand ) ) && ! empty( $request -> brand ) ) {
   
            $brand = '['. $request -> brand .']';
   
         }

         foreach( $check as $calendar_notes ) {
            $x0 = 'en';
            $x1 = 'it';
            $x2 = 'de';
            $x3 = 'ph-fil';
            $x4 = 'ph-bis';
            for( $x = 0; $x < 5; $x++ ) {
               $languange = $x+$x;
                  
               $get_title = getTranslation($calendar_notes -> title, $languange);

               foreach( json_decode( $calendar_notes -> brands ) as $c ) {
                     
                  foreach( json_decode( $brand ) as $br ) {
                     
                     if( strtolower( $get_title ) == strtolower( $title ) && $calendar_notes -> id != $request->id && $br == $c ){
                        
                        return response()->json( [ 'duplicate' => true, 'name' => $title ] );
      
                     }
                     
                  }
      
               }

            }

         }

         if (is_null($request->id)) {

            $calendar_notes = new CalendarNotes;

            $calendar_notes -> title = json_encode([
                                          $lang => $title,
                                       ]);

            $calendar_notes -> content = $request -> content;

            $calendar_notes -> type = $request -> type;

            $calendar_notes -> target_date_from = $request -> target_date_from;
            
            $calendar_notes -> target_date_to = $request -> target_date_to;

            $calendar_notes -> target_location = $request -> target_location;

            $calendar_notes -> brands = $brand;

            $calendar_notes -> save();

            $calendar_notes -> name = $calendar_notes -> title;

         } else {

            $calendar_notes = CalendarNotes::findOrFail($request->id);

            $calendar_notesVal = string_add_json( $lang, ucwords( $request -> title ), string_remove( $lang, $calendar_notes -> title ) );

            $calendar_notes -> title = $calendar_notesVal;

            $calendar_notes -> content = ( isset( $request -> content ) && ! empty( $request -> content ) ? $request -> content : null );

            $calendar_notes -> type = $request -> type;

            $calendar_notes -> target_date_from = $request -> target_date_from;
            
            $calendar_notes -> target_date_to = $request -> target_date_to;

            $calendar_notes -> target_location = $request -> target_location;
            
            $calendar_notes -> brands = $request -> brand;

            $calendar_notes->update();
            
            $calendar_notes->name = $calendar_notes -> title;


         }

         $calendar_notes_id = $calendar_notes -> id;
         
         CalendarNotesLocation::where('calendar_notes_id', '=', $request->id ) -> delete();

         if( $request -> target_location == 'Selected Location') {

            foreach( json_decode( $request -> country ) as $country ) {

                  $location = new CalendarNotesLocation;

                  $location -> calendar_notes_id = $calendar_notes_id;

                  $location -> country = $country;

                  $location -> save();

            }

         }

         return response()->json($calendar_notes);

      } catch( \Exception $e ) {

         echo "<pre>";

         echo $e -> getMessage();

         echo "</pre>";

      }

   }

   public function destroy(Request $request)
   {

      CalendarNotes::where('id',$request->id)->delete();

      CalendarNotesLocation::where('calendar_notes_id',$request->id)->delete();

      return response()->json(true);

   }

   public function getName( Request $request ) {
      
      $calendar_notes = CalendarNotes::whereId( $request->id ) -> first();
      
      $message = ucwords( string_to_value( $request->lang , $calendar_notes->title) );
      
      return response() -> json($message);

  }

  public function linkBrand( Request $request ) 
  {

      try{
         
      $lang = $request->lang;

      $calendar_notes = CalendarNotes::findOrFail($request->id);

      $calendar_notes -> brands = $request -> brand;
      
         $calendar_notes -> update();
            
         $calendar_notes -> name = ucwords( string_to_value( $lang, $calendar_notes -> title ) );

         return response()->json( $calendar_notes );

      } catch( \Exception $e ) {

         echo "<pre>";
         
         echo $e -> getMessage();
         
         echo "</pre>";
      
      }
   
   }

   public function getNameCalendarNotes( $id, $lang ) {
        
      $reasons = CalendarNotes::whereId( $id ) -> first();

      $name = ucwords( string_to_value( $lang, $reasons -> title) );

      return $name;

  }

  public function getNameCalendarNotesType( $id, $lang ) {
      $name = '';
      $calendar_notes_type = CalendarNotesType::whereId( $id ) -> first();

      if( ! empty( $calendar_notes_type -> name ) ) {
         $name = ucwords( string_to_value( $lang, $calendar_notes_type -> name) );
      }
      return $name;

   }

}
