<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Tags;

use App\Models\LanguageValidator;

class TagsController extends Controller
{
    

    public function index() {

        return view('backend.tags.index');

    }


    public function getList( Request $request ) {
        
        $lang = $request -> input( 'lang' );

        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } 
        if( $lang == 'de' ) { $language = 'German'; }
        if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
        if( $lang == 'ph-bis' ) { $language = 'Visayan'; }
        
        $get_tags = Tags::orderBy( 'id', 'DESC' ) -> get();
        
        $tags = array();

        foreach( $get_tags as $datas ) {

            $name = $this -> getName( $datas[ 'id' ], $lang );
            
            $base_name = ( ! empty( $name ) ? $name : $this -> getName( $datas[ 'id' ], 'en' ) );

            if( $name == '' && $base_name == '' ) {

                $base_name_italy = $this -> getName( $datas[ 'id' ], 'it' );

                if( $base_name_italy !== '' ) {

                    $base_name = $base_name_italy;

                } else {

                    // $base_name = $this -> getName( $datas[ 'id' ], 'de' );

                  $base_name_germany = $this -> getName( $datas[ 'id' ], 'de' );

                  if( $base_name_germany !== '' ) {

                    $base_name = $base_name_germany;
 
                  }

                  else 

                  {

                     $base_name_filipino = $this -> getName( $datas[ 'id' ], 'ph-fil' );

                     if( $base_name_filipino !== '' ) {

                        $base_name = $base_name_filipino;
   
                     }

                     else{

                        $base_name = $this -> getName( $datas[ 'id' ], 'ph-bis' );
                        
                     }

                  }

                }
                
                
            }
            
            array_push( $tags, [

                'id' => $datas[ 'id' ],

                'name' => $base_name,

                'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                'convertion' => ( ! empty( $name ) ? false : true ),

                'language' => $language

            ] );

        }
        
        return response() -> json( $tags );

    }


    public function postTags( Request $request ) {

        $input = json_decode( $request -> input( 'data' ) );

        $lang = $request -> input( 'lang' );

        $tags_check = new Tags;

        if ( $tags_check -> count() > 0 ) {

            foreach ( $tags_check -> get() as $tags_checks ) {

                $tags_checks_ = $this -> view_tags( $tags_checks, $lang );

                if(  ucwords( $tags_checks_[ 'name' ] ) == ucwords( $input -> name ) ) {

                    $message = ucwords( string_to_value( $lang, $input -> name ) );

                    return response() -> json( alert_duplicate( $message, $input ) );

                }

            }

        }

        $formData = array(

            'name' => ucwords( $input -> name )

        );

        $jsonData = string_to_json( $lang, [ 'name' ], $formData );
        
        $tags = $tags_check -> create( $jsonData );

        if ( $tags ) {

            $message = ucwords( string_to_value( $lang, $tags[ 'name' ] ) );

            return response() -> json( alert_success( $message, $tags ) );

        }

    }


    public function updateTags( Request $request ) {

        $formData = json_decode( $request -> input( 'data' ) );

        if( ! empty (  $formData -> id ) ) {

            $lang = $formData -> language;

            $tags_check = new Tags;

            if ( $tags_check -> count() > 0 ) {

                foreach ( $tags_check -> get() as $tags_checks ) {

                    $tags_checks_ = $this -> view_tags( $tags_checks, $lang );

                    if( ucwords( $tags_checks_[ 'name' ] ) == ucwords( $formData -> name ) && $tags_checks_[ 'id' ] != $formData -> id ) {

                        $message = ucwords( string_to_value( $lang, $formData -> name ) );

                        return response() -> json( alert_duplicate( $message, $formData ) );

                    }

                }

            }

            $respQuestion = $tags_check -> changeData( $request );
           
            if ( $respQuestion ) {

                $message = ucwords( string_to_value( $lang, $respQuestion -> name));

                return response() -> json( alert_update( $message, $respQuestion ) );

            }

        }

    }
    

    public function deleteTags( Request $request, $id ) {

        $lang = $request -> input( 'lang' );

        $tags = Tags::whereId( $id );

        $name = $tags -> select('name') -> first() -> name;

        if ( $tags -> delete() ) {

            $message = ucwords( string_to_value( $lang, $name ) );

            return response() -> json( alert_delete( $message ) );

        }

    }

    
    public function getTagsName( Request $request, $id, $lang ) {

        $tags = Tags::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $tags -> name) );

        return response() -> json( [ 'name' => $message ] );

    }


    public function view_tags( $data, $lang ) {

        return [

        'id' => $data -> id,

        'name' => string_to_value( $lang, $data -> name ),

        'created_at' => $data -> created_at,

        'updated_at' => $data -> updated_at,

        ];

    }

    
    public function show_data( $lang ) {

        $tags = Tags::selectRaw( "id, name, created_at, updated_at" ) -> get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'name', 'created_at', 'updated_at' ], $tags );

        return $json;

    }

    public function getName( $id, $lang ) {
        
        $tags = Tags::whereId( $id ) -> first();

        $name = ucwords( string_to_value( $lang, $tags -> name) );

        return $name;

    }

}
