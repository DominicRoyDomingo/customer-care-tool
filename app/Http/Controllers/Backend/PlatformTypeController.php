<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\PlatformType;

use App\Models\LanguageValidator;

class PlatformTypeController extends Controller
{


    public function index() {

        return view('backend.platform.type.index');

    }


    public function getList( Request $request ) {
        
        $lang = $request -> input( 'lang' );

        $language = 'English';

        if( $lang == 'it') { $language = 'Italian'; } 
        if( $lang == 'de' ) { $language = 'German'; }
        if( $lang == 'ph-fil' ) { $language = 'Filipino'; }
        if( $lang == 'ph-bis' ) { $language = 'Visayan'; }
        // $show_data = $this -> show_data( $lang );

        $platform_type = PlatformType::orderBy( 'id', 'DESC' ) -> get();
        
        $platform = array();

        foreach( $platform_type as $datas ) {

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
            
            array_push( $platform, [

                'id' => $datas[ 'id' ],

                'name' => $base_name,

                'created_at' => date( 'd/m/Y', strtotime( $datas[ 'created_at' ] ) ),

                'updated_at' => date( 'd/m/Y', strtotime( $datas[ 'updated_at' ] ) ),

                'convertion' => ( ! empty( $name ) ? false : true ),

                'language' => $language

            ] );

        }

        return response() -> json( $platform );

    }


    public function postPlatform( Request $request ) {

        $input = json_decode( $request -> input( 'data' ) );

        $lang = $request -> input( 'lang' );

        $platform_check = new PlatformType;

        if ( $platform_check -> count() > 0 ) {

            foreach ( $platform_check -> get() as $platform_checks ) {

                $platform_checks_ = $this -> view_platform_type( $platform_checks, $lang );

                if(  ucwords( $platform_checks_[ 'name' ] ) == ucwords( $input -> name ) ) {

                    $message = ucwords( string_to_value( $lang, $input -> name ) );

                    return response() -> json( alert_duplicate( $message, $input ) );

                }

            }

        }

        $formData = array(

            'name' => ucwords( $input -> name )

        );

        $jsonData = string_to_json( $lang, [ 'name' ], $formData );

        $platform = $platform_check -> create( $jsonData );

        if ( $platform ) {

            $message = ucwords( string_to_value( $lang, $platform[ 'name' ] ) );

            return response() -> json( alert_success( $message, $platform ) );

        }

    }


    public function updatePlatform( Request $request ) {

        $formData = json_decode( $request -> input( 'data' ) );

        if( ! empty (  $formData -> id ) ) {

            $lang = $formData -> language;

            $platform_check = new PlatformType;

            if ( $platform_check -> count() > 0 ) {

                foreach ( $platform_check -> get() as $platform_checks ) {

                    $platform_checks_ = $this -> view_platform_type( $platform_checks, $lang );

                    if( ucwords( $platform_checks_[ 'name' ] ) == ucwords( $formData -> name ) && $platform_checks_[ 'id' ] != $formData -> id ) {

                        $message = ucwords( string_to_value( $lang, $formData -> name ) );

                        return response() -> json( alert_duplicate( $message, $formData ) );

                    }

                }

            }

            $respQuestion = $platform_check -> changeData( $request );

            if ( $respQuestion ) {

                $message = ucwords( string_to_value( $lang, $respQuestion -> name));

                return response() -> json( alert_update( $message, $respQuestion ) );

            }

        }

    }
    

    public function deletePlatform( Request $request, $id ) {

        $lang = $request -> input( 'lang' );

        $platform = PlatformType::whereId( $id );

        $name = $platform -> select('name') -> first() -> name;

        if ( $platform -> delete() ) {

            $message = ucwords( string_to_value( $lang, $name ) );

            return response() -> json( alert_delete( $message ) );

        }

    }

    
    public function getPlatformName( Request $request, $id, $lang ) {

        $platform = PlatformType::whereId( $id ) -> first();

        $message = ucwords( string_to_value( $lang, $platform -> name) );

        return response() -> json( [ 'name' => $message ] );

    }
    

    public function view_platform_type( $data, $lang ) {

        return [

        'id' => $data -> id,

        'name' => string_to_value( $lang, $data -> name ),

        'created_at' => $data -> created_at,

        'updated_at' => $data -> updated_at,

        ];

    }


    public function show_data( $lang ) {

        $platform = PlatformType::selectRaw( "id, name, created_at, updated_at" ) -> get();

        $language_setting = new LanguageValidator;

        $json = $language_setting -> get_data( $lang  , [ 'id', 'name', 'created_at', 'updated_at' ], $platform );

        return $json;
        
    }

    public function getName( $id, $lang ) {
        
        $platform_type = PlatformType::whereId( $id ) -> first();

        $name = ucwords( string_to_value( $lang, $platform_type -> name) );

        return $name;

    }

}
